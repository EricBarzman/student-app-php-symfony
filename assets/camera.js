import CameraPhoto, { FACING_MODES, IMAGE_TYPES } from 'jslib-html5-camera-photo';

const IMAGE_RATIO = 0.5; 
const startCameraBtn = document.getElementById('start-camera');
const takePhotoBtn = document.getElementById('click-photo');
const deleteBtn = document.getElementById('delete-photo');

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

const photoHiddenInput = document.getElementById('photo-hidden');
let image_data_url = ''

let cameraPhoto = new CameraPhoto(video);

startCameraBtn.addEventListener('click', async () => {
    video.classList.toggle('d-none');
    takePhotoBtn.classList.toggle('d-none');
    startCameraBtn.textContent = startCameraBtn.textContent == "Stop camera" ? "Start camera" : "Stop camera";
    
    cameraPhoto.enumerateCameras()
        .then((cameras)=>{
            cameras.forEach((camera) => {
                let {kind, label, deviceId} = camera;
                let cameraStr = `
                        kind: ${kind}
                        label: ${label}
                        deviceId: ${deviceId}
                    `;
                alert(cameraStr);
            });
        })
    
    cameraPhoto.startCamera(FACING_MODES.ENVIRONMENT, {})
    .then((stream)=>{/* ... */});
});
takePhotoBtn.addEventListener('click', () => {
    //Display photo and fill input
    canvas.classList.remove('d-none');
    canvas.width = video.videoWidth* IMAGE_RATIO;
    canvas.height = video.videoHeight * IMAGE_RATIO;
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    image_data_url = canvas.toDataURL("image/jpeg", 0.8);
    photoHiddenInput.value = image_data_url;

    // Shut off camera
    takePhotoBtn.classList.toggle('d-none');
    video.classList.toggle('d-none');
    video.srcObject = null;
    startCameraBtn.textContent = "Start camera";

    //Turn on delete button
    if (deleteBtn.classList.contains('d-none')) deleteBtn.classList.toggle('d-none');
});

deleteBtn.addEventListener('click', () => {
    if (!canvas.classList.contains('d-none')) canvas.classList.add('d-none');
    photoHiddenInput.value = null;
    deleteBtn.classList.toggle('d-none');
})