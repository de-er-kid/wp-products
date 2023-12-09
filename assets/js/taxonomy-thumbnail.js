jQuery(document).ready(function($){
    var mediaUploader;

    // When the user clicks on the "Add Image" button
    $('.taxonomy_thumbnail_button').click(function(e) {
        e.preventDefault();

        // If the media uploader object has already been created, reopen it.
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create a new media uploader object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: taxonomyThumbnail.frameTitle,
            button: {
                text: taxonomyThumbnail.buttonText,
            },
            multiple: false
        });

        // When a file is selected, grab the attachment ID and set it as the value of our hidden input field
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#taxonomy_thumbnail_preview').attr('src', attachment.url).css('display', 'block');
            $('#taxonomy_thumbnail').val(attachment.id);
        });

        // Open the media uploader
        mediaUploader.open();
    });
});