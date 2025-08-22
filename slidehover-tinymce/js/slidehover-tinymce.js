(function() {
    tinymce.PluginManager.add('slidehover', function(editor, url) {

        editor.addButton('slidehover', {
            title: 'Inserisci Slide Hover',
            icon: 'dashicon dashicons-images-alt2',
            onclick: function() {

                var frame = wp.media({
                    title: 'Seleziona le immagini (prima e dopo)',
                    multiple: true,
                    library: { type: 'image' }
                });

                frame.on('select', function() {
                    var selection = frame.state().get('selection');
                    if (selection.length !== 2) {
                        alert('Seleziona esattamente DUE immagini.');
                        return;
                    }

                    var ids = selection.pluck('id');
                    var shortcode = '[slidehover before="' + ids[0] + '" after="' + ids[1] +
                                    '" width="600" height="400" loop="no"]';

                    editor.insertContent(shortcode);
                });

                frame.open();
            }
        });
    });
})();