const video = document.getElementById('video');

// function startup(){
//     navigator.mediaDevices.getUserMedia({
//         audio: false,
//         video: true
//     }).then(stream => {
//         video.srcObject = stream;
//     }).catch(console.error);
// }

window.addEventListener('load', startup, false);

// Prefer camera resolution nearest to 1280x720.
const constraints = { audio: true, video: { width: 1280, height: 720 } };
// var constraints = { video: { facingMode: "environment" } };

navigator.mediaDevices.getUserMedia(constraints)
.then(function(mediaStream) {
  const video = document.querySelector('video');
  video.srcObject = mediaStream;
  video.onloadedmetadata = function(e) {
    video.play();
  };
})
.catch(function(err) { console.log(err.name + ": " + err.message); }); // always check for errors at the end.
