jQuery(function($){
    /* helper: usa l’URL originale per avere dimensioni complete */
    function pick(btn, imgSel, hidSel){
        var frame = wp.media({title:'Seleziona immagine', multiple:false, library:{type:'image'}});
        frame.on('select', function(){
            var att = frame.state().get('selection').first().toJSON();
            $(imgSel).attr('src', att.url);   // immagine a piena risoluzione
            $(hidSel).val(att.id);
        });
        frame.open();
    }

    /* aggiorna anteprima live */
    function updatePreview(){
        var w = parseInt($('#sh-width').val(), 10) || 600;
        var h = parseInt($('#sh-height').val(), 10) || 400;
        $('#sh-preview').css({
            width  : Math.min(w, 480),   // larghezza massima anteprima
            height : Math.min(h, 250),   // altezza massima anteprima
            backgroundSize : 'contain',  // niente taglio
            backgroundRepeat: 'no-repeat',
            backgroundPosition: 'center'
        });
    }

    tinymce.PluginManager.add('slidehover', function(ed){
        ed.addButton('slidehover', {
            title : 'Slide Hover',
            icon  : 'dashicon dashicons-images-alt2',
            onclick : function(){
                ed.windowManager.open({
                    title   : 'Slide Hover',
                    width   : 660,   // più larga
                    height  : 460,   // più bassa
                    body    : [
                        {type:'container', layout:'flex', direction:'row', style:'margin-bottom:8px', items:[
                            {type:'container', flex:1, label:'Prima', items:[
                                {type:'button', name:'pickBefore', text:'Scegli', onclick:function(){pick('before', '#sh-prev-before', '#sh-id-before');}},
                                {type:'textbox', subtype:'hidden', name:'sh_id_before', id:'sh-id-before'},
                                {type:'container', id:'sh-prev-before', style:'width:80px;height:60px;background:#eee;border:1px solid #ccc;display:inline-block;background-size:cover'}
                            ]},
                            {type:'container', flex:1, label:'Dopo', items:[
                                {type:'button', name:'pickAfter',  text:'Scegli', onclick:function(){pick('after', '#sh-prev-after', '#sh-id-after');}},
                                {type:'textbox', subtype:'hidden', name:'sh_id_after',  id:'sh-id-after'},
                                {type:'container', id:'sh-prev-after',  style:'width:80px;height:60px;background:#eee;border:1px solid #ccc;display:inline-block;background-size:cover'}
                            ]}
                        ]},
                        {type:'container', layout:'flex', direction:'row', items:[
                            {type:'textbox', subtype:'number', name:'sh_width',  label:'W (px)', value:600, min:100, max:2000, onchange:updatePreview},
                            {type:'textbox', subtype:'number', name:'sh_height', label:'H (px)', value:400, min:100, max:2000, onchange:updatePreview}
                        ]},
                        {type:'listbox', name:'sh_effect', label:'Effetto', values:[
                            {text:'Fade', value:'fade'},
                            {text:'Loop', value:'cycle'},
                            {text:'Swipe', value:'swipe'},
                            {text:'Zoom', value:'zoom'}
                        ]},
                        {type:'textbox', subtype:'number', name:'sh_dur',  label:'Durata (s)', value:0.5, min:0.1, max:5, step:0.1},
                        {type:'listbox', name:'sh_align', label:'Allineamento', values:[
                            {text:'None', value:'none'},
                            {text:'Left', value:'left'},
                            {text:'Center', value:'center'},
                            {text:'Right', value:'right'}
                        ]},
                        {type:'textbox', multiline:true, name:'sh_caption', label:'Caption', style:'width:100%'},
                        {type:'container', html:'<div id="sh-preview" style="width:100%;height:200px;background:#eee;border:1px solid #ccc;background-size:contain;background-repeat:no-repeat;background-position:center;border-radius:4px"></div>'}
                    ],
                    onsubmit : function(e){
                        if(!e.data.sh_id_before || !e.data.sh_id_after){
                            alert('Scegli entrambe le immagini');
                            return false;
                        }
                        var sc = '[slidehover before="'+e.data.sh_id_before+
                                 '" after="'+e.data.sh_id_after+
                                 '" width="'+e.data.sh_width+
                                 '" height="'+e.data.sh_height+
                                 '" effect="'+e.data.sh_effect+
                                 '" dur="'+e.data.sh_dur+
                                 '" align="'+e.data.sh_align+'"]'+
                                 (e.data.sh_caption || '')+
                                 '[/slidehover]';
                        ed.insertContent(sc);
                    }
                });
            }
        });
    });
});