<style>
    html, body {
        margin: 0!important;
        padding: 0!important;
    }
</style>

<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

<br>

<button id="btn-start-recording">Start Recording</button>
<button id="btn-stop-recording" disabled>Stop Recording</button>
<button id="btn-release-microphone" disabled>Release Microphone</button>
<button id="btn-download-recording" disabled>Download</button>
<div><audio controls autoplay ></audio></div>
<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>
<script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
<script>
var audio = document.querySelector('audio');

function captureMicrophone(callback) {
    btnReleaseMicrophone.disabled = false;

    if(microphone) {
        callback(microphone);
        return;
    }

    if(typeof navigator.mediaDevices === 'undefined' || !navigator.mediaDevices.getUserMedia) {
        alert('This browser does not supports WebRTC getUserMedia API.');

        if(!!navigator.getUserMedia) {
            alert('This browser seems supporting deprecated getUserMedia API.');
        }
    }

    navigator.mediaDevices.getUserMedia({
        audio: true
    }).then(function(mic) {
        alert(mic.id);
        callback(mic);
    }).catch(function(error) {
        alert('Unable to capture your microphone. Please check console logs.');
        alert(error);
        console.error(error);
    });
}

function replaceAudio(src) {
    var newAudio = document.createElement('audio');
    newAudio.controls = true;

    if(src) {
        newAudio.src = src;
    }
    
    var parentNode = audio.parentNode;
    parentNode.innerHTML = '';
    parentNode.appendChild(newAudio);

    audio = newAudio;
}

function stopRecordingCallback(audioURL) {
    replaceAudio(URL.createObjectURL(recorder.getBlob()));

    btnStartRecording.disabled = false;

    setTimeout(function() {
        if(!audio.paused) return;

        setTimeout(function() {
            if(!audio.paused) return;
            audio.play();
        }, 1000);
        
        audio.play();
    }, 3000);

    audio.play();

    btnDownloadRecording.disabled = false;

    if(isSafari) {
        click(btnReleaseMicrophone);
    }
}

var isEdge = navigator.userAgent.indexOf('Edge') !== -1 && (!!navigator.msSaveOrOpenBlob || !!navigator.msSaveBlob);
var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);

var recorder; // globally accessible
var microphone;

var btnStartRecording = document.getElementById('btn-start-recording');
var btnStopRecording = document.getElementById('btn-stop-recording');
var btnReleaseMicrophone = document.querySelector('#btn-release-microphone');
var btnDownloadRecording = document.getElementById('btn-download-recording');

btnStartRecording.onclick = function() {
    this.disabled = true;
    this.style.border = '';
    this.style.fontSize = '';
    alert(isSafari);
    if (!microphone) {
        captureMicrophone(function(mic) {
            microphone = mic;            
            if(isSafari) {
                replaceAudio();

                audio.muted = true;
                setSrcObject(microphone, audio);
                audio.play();

                btnStartRecording.disabled = false;
                btnStartRecording.style.border = '1px solid red';
                btnStartRecording.style.fontSize = '150%';

                alert('Please click startRecording button again. First time we tried to access your microphone. Now we will record it.');
                return;
            }

            click(btnStartRecording);
        });
        return;
    }

    
    replaceAudio();

    audio.muted = true;
    setSrcObject(microphone, audio);
    audio.play();

    var options = {
        mimeType: 'audio/webm',
        type: 'audio',
        numberOfAudioChannels: isEdge ? 1 : 2,
        checkForInactiveTracks: true,
        bufferSize: 16384
    };

    if(isSafari || isEdge) {
        options.recorderType = StereoAudioRecorder;
    }

    if(navigator.platform && navigator.platform.toString().toLowerCase().indexOf('win') === -1) {
        options.sampleRate = 48000; // or 44100 or remove this line for default
    }

    if(isSafari) {
        options.sampleRate = 44100;
        options.bufferSize = 4096;
        options.numberOfAudioChannels = 2;
    }

    if(recorder) {
        recorder.destroy();
        recorder = null;
    }

    recorder = RecordRTC(microphone, options);

    recorder.startRecording();

    btnStopRecording.disabled = false;
    btnDownloadRecording.disabled = true;
};

btnStopRecording.onclick = function() {
    this.disabled = true;
    recorder.stopRecording(stopRecordingCallback);
};

btnReleaseMicrophone.onclick = function() {
    this.disabled = true;
    btnStartRecording.disabled = false;

    if(microphone) {
        microphone.stop();
        microphone = null;
    }

    if(recorder) {
        // click(btnStopRecording);
    }
};

btnDownloadRecording.onclick = function() {
    this.disabled = true;
    if(!recorder || !recorder.getBlob()) return;

    if(isSafari) {
        recorder.getDataURL(function(dataURL) {
            SaveToDisk(dataURL, getFileName('mp3'));
        });
        return;
    }

    var blob = recorder.getBlob();
    var file = new File([blob], getFileName('mp3'), {
        type: 'audio/mp3'
    });
    invokeSaveAsDialog(file);
    /*
    invokeSaveAsDialog(file, fileName)
        file        Blob        File or Blob object. This parameter is required.
        fileName    string      Optional file name e.g. "Recorded-Video.webm"
    */
};

function click(el) {
    el.disabled = false; // make sure that element is not disabled
    var evt = document.createEvent('Event');
    evt.initEvent('click', true, true);
    el.dispatchEvent(evt);
}

function getRandomString() {
    if (window.crypto && window.crypto.getRandomValues && navigator.userAgent.indexOf('Safari') === -1) {
        var a = window.crypto.getRandomValues(new Uint32Array(3)),
            token = '';
        for (var i = 0, l = a.length; i < l; i++) {
            token += a[i].toString(36);
        }
        return token;
    } else {
        return (Math.random() * new Date().getTime()).toString(36).replace(/\./g, '');
    }
}

function getFileName(fileExtension) {
    var d = new Date();
    var year = d.getFullYear();
    var month = d.getMonth();
    var date = d.getDate();
    return 'RecordRTC-' + year + month + date + '-' + getRandomString() + '.' + fileExtension;
}

function SaveToDisk(fileURL, fileName) {
    // for non-IE
    if (!window.ActiveXObject) {
        var save = document.createElement('a');
        save.href = fileURL;
        save.download = fileName || 'unknown';
        save.style = 'display:none;opacity:0;color:transparent;';
        (document.body || document.documentElement).appendChild(save);

        if (typeof save.click === 'function') {
            save.click();
        } else {
            save.target = '_blank';
            var event = document.createEvent('Event');
            event.initEvent('click', true, true);
            save.dispatchEvent(event);
        }

        (window.URL || window.webkitURL).revokeObjectURL(save.href);
    }

    // for IE
    else if (!!window.ActiveXObject && document.execCommand) {
        var _window = window.open(fileURL, '_blank');
        _window.document.close();
        _window.document.execCommand('SaveAs', true, fileName || fileURL)
        _window.close();
    }
}
</script>

<footer style="margin-top: 20px;"><small id="send-message"></small></footer>
<script src="https://cdn.webrtc-experiment.com/common.js"></script>