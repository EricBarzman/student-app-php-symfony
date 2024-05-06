import { Html5QrcodeScanner } from "html5-qrcode";

function domReady(fn) {
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1000);
    } else document.addEventListener("DOMContentLoaded", fn);
}
domReady(function () {   
    function onScanSuccess(decodeText, decodeResult) {
        htmlscanner.pause();
        const searchForm = document.getElementById('search')
        const searchElement = document.getElementById('search-id')
        searchElement.value = decodeText;
        searchForm.submit();
    }

    let qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
        let minEdgePercentage = 0.7; // 70%
        let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
        let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
        return {
            width: qrboxSize,
            height: qrboxSize
        };
    }


    let htmlscanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: qrboxFunction }
    );
    htmlscanner.render(onScanSuccess);
});