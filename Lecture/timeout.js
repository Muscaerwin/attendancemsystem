var labels = [];
let detectedFaces = [];
let videoStream;

function updateTable() {
    var selectedCourseID = document.getElementById('courseSelect').value;
    var selectedUnitCode = document.getElementById('unitSelect').value;
    var selectedVenue = document.getElementById("venueSelect").value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'manageFolder.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                labels = response.data;
                if (selectedCourseID && selectedUnitCode && selectedVenue) {
                    updateOtherElements();
                }
                document.getElementById('studentTableContainer').innerHTML = response.html;
            } else {
                console.error('Error:', response.message);
            }
        }
    };

    xhr.send('courseID=' + encodeURIComponent(selectedCourseID) +
             '&unitID=' + encodeURIComponent(selectedUnitCode) +
             '&venueID=' + encodeURIComponent(selectedVenue));
}

function markAttendance(detectedFaces) {
    document.querySelectorAll('#studentTableContainer tr').forEach(row => {
        const registrationNumber = row.cells[0].innerText.trim();
        if (detectedFaces.includes(registrationNumber)) {
            row.cells[5].innerText = 'present';
            // Record timeOuts when marked present
            const currentTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            row.cells[6].innerText = currentTime; // Assuming timeOuts is in the 7th column
        } else {
            row.cells[5].innerText = 'absent';
            row.cells[6].innerText = ''; // Clear timeOuts if absent
        }
    });
}

function updateOtherElements() {
    const video = document.getElementById("video");
    const videoContainer = document.querySelector(".video-container");
    const startButton = document.getElementById("startButton");

    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri("http://localhost/models"),
        faceapi.nets.faceRecognitionNet.loadFromUri("http://localhost/models"),
        faceapi.nets.faceLandmark68Net.loadFromUri("http://localhost/models"),
    ]).then(() => {
        startButton.addEventListener("click", () => {
            videoContainer.style.display = "flex";
            startWebcam();
        });
    });

    function startWebcam() {
        navigator.mediaDevices
            .getUserMedia({
                video: true,
                audio: false,
            })
            .then((stream) => {
                video.srcObject = stream;
                videoStream = stream; 
                video.play();
                startFaceRecognition();
            })
            .catch((error) => {
                console.error(error);
            });
    }

    async function getLabeledFaceDescriptions() {
        const labeledDescriptors = [];
        for (const label of labels) {
            const descriptions = [];
            for (let i = 1; i <= 2; i++) {
                try {
                    const img = await faceapi.fetchImage(`./labels/${label}/${i}.png`);
                    const detections = await faceapi
                        .detectSingleFace(img)
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                    
                    if (detections) {
                        descriptions.push(detections.descriptor);
                    }
                } catch (error) {
                    console.error(`Error processing ${label}/${i}.png:`, error);
                }
            }
            if (descriptions.length > 0) {
                labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(label, descriptions));
            }
        }
        return labeledDescriptors;
    }

    video.addEventListener("play", async () => {
        const labeledFaceDescriptors = await getLabeledFaceDescriptions();
        const faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
        const canvas = faceapi.createCanvasFromMedia(video);
        videoContainer.appendChild(canvas);
        const displaySize = { width: video.width, height: video.height };
        faceapi.matchDimensions(canvas, displaySize);

        setInterval(async () => {
            const detections = await faceapi
                .detectAllFaces(video)
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

            const results = resizedDetections.map((d) => {
                return faceMatcher.findBestMatch(d.descriptor);
            });
            detectedFaces = results.map(result => result.label);
            markAttendance(detectedFaces);
            results.forEach((result, i) => {
                const box = resizedDetections[i].detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: result });
                drawBox.draw(canvas);
            });
        }, 100);
    });
}

function sendTimeOutsDataToServer() {
    const timeOutsData = [];

    document.querySelectorAll('#studentTableContainer tr').forEach((row, index) => {
        if (index === 0) return; // Skip header row
        const studentID = row.cells[0].innerText.trim();
        const course = row.cells[2].innerText.trim();
        const unit = row.cells[3].innerText.trim();
        const attendanceStatusTimeout = row.cells[5].innerText.trim(); // Updated variable name
        const timeOuts = row.cells[6].innerText.trim(); // Assuming timeOuts is in the 7th column

        if (attendanceStatusTimeout === 'present' && timeOuts) {
            timeOutsData.push({ studentID, course, unit, attendanceStatusTimeout, timeOuts }); // Updated variable name
        }
    });

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'takeAttendanceTimeout.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                showMessage('Time Outs recorded successfully.');
            } else {
                showMessage('Error: Unable to record Time Outs.');
            }
        }
    };

    xhr.send(JSON.stringify(timeOutsData));
}

function showMessage(message) {
    var messageDiv = document.getElementById('messageDiv');
    messageDiv.style.display = "block";
    messageDiv.innerHTML = message;
    console.log(message);
    messageDiv.style.opacity = 1;
    setTimeout(function() {
        messageDiv.style.opacity = 0;
    }, 5000);
}

function stopWebcam() {
    if (videoStream) {
        const tracks = videoStream.getTracks();
        tracks.forEach((track) => {
            track.stop();
        });
        video.srcObject = null;
        videoStream = null;
    }
}

document.getElementById("endAttendance").addEventListener("click", function() {
    sendTimeOutsDataToServer();
    const videoContainer = document.querySelector(".video-container");
    videoContainer.style.display = "none";
    stopWebcam();
});

// Optional: Fetch attendance records
document.getElementById('fetchAttendanceForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch('your_php_file.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const tableBody = document.getElementById('attendanceRecordsTable').querySelector('tbody');
        tableBody.innerHTML = ''; // Clear previous records

        data.forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${record.timeOuts || 'N/A'}</td>
                <td>${record.attendanceStatusTimeout}</td> <!-- Updated variable name -->
            `;
            tableBody.appendChild(row);
        });
    })
    .catch(error => console.error('Error fetching attendance:', error));
});
