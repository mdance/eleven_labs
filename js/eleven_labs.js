(function ($, Drupal) {
  'use strict';

  var ns = 'eleven_labs';

  Drupal.behaviors[ns] = {
    attach: function (context, settings) {
      var subsettings;

      subsettings = settings[ns];

      once(ns, 'body').forEach(
        function(element) {
          //const upload = document.getElementById('edit-files-upload');
          const upload = document.getElementById('edit-files');
          const player = document.getElementById('player');
          const toggle = document.getElementById('edit-toggle');

          let stream = null;
          let recorder = null;

          toggle.addEventListener(
            'click',
            async function(event) {
              event.preventDefault();

              const value = toggle.value;

              if (value === subsettings.label_start) {
                if (stream === null) {
                  const constraints = {
                    video: false,
                    audio: true,
                  };

                  stream = await navigator.mediaDevices.getUserMedia(constraints);

                  const options = {
                    mimeType: 'audio/webm',
                  }

                  recorder = new MediaRecorder(stream, options);

                  recorder.addEventListener(
                    'dataavailable',
                    event => {
                      const data = event.data;
                      const url = URL.createObjectURL(data);

                      player.src = url;

                      const blob = new Blob([data], {type: data.type});
                      const file = new File([blob], 'upload.webm', {type: blob.type});

                      const dataTransfer = new DataTransfer();

                      dataTransfer.items.add(file);

                      upload.files = dataTransfer.files;
                    }
                  );
                }

                recorder.start();
                toggle.value = subsettings.label_stop;
              }
              else {
                recorder.stop();

                stream.getTracks().forEach(
                  track => {
                    track.stop();
                  }
                );

                stream = null;

                toggle.value = subsettings.label_start;
              }
            }
          );
        }
      );
    }
  }
})(jQuery, Drupal, drupalSettings);
