$(function() {
    var defaultFont = 'Century Gothic, Tahoma, Arial, sans-serif';
    var defautSize = '12pt';

    var challengerFonts = [
        'Agency FB=agency fb',
        'Century Gothic=century gothic',
        'Calibri=calibri',
        'Tahoma=tahoma',
        'Arial=arial,helvetica,sans-serif',
        'Courier New=courier new'];

    var challengerSizes = [
        '8pt',
        '10pt',
        '12pt',
        '14pt'];

    var challengerMenu = [
        {text: 'Participant', menu: [
            {text: 'Prénom', value: {name:'Participant : Prénom', field:'p.prenom'}},
            {text: 'Prénom / Nom', value: {name:'Participant : Prénom / Nom', field:'p.prenomnom'}},
            {text: 'Email', value: {name:'Participant : Email', field:'p.email'}},
            {text: 'Téléphone', value: {name:'Participant : Téléphone', field:'p.telephone'}},
            {text: 'Licence', value: {name:'Participant : Licence', field:'p.licence'}},
            {text: 'Clé (URL)', value: {name:'Participant : Clé', field:'p.cle', simple:true}},
            {text: '-'},
            {text: 'Sexe (f/h)', value: {name:'Participant : Sexe', field:'p.sexe', bool:true}},
            {text: 'Sportif (0/1)', value: {name:'Participant : Sportif', field:'p.sportif', bool:true}},
            {text: 'Fanfaron (0/1)', value: {name:'Participant : Fanfaron', field:'p.fanfaron', bool:true}},
            {text: 'Pompom (0/1)', value: {name:'Participant : Pompom', field:'p.pompom', bool:true}},
            {text: 'Caméraman (0/1)', value: {name:'Participant : Caméraman', field:'p.cameraman', bool:true}}]},

         {text: 'Inscription', menu: [
            {text: 'Tarif', value: {name:'Inscription : Tarif', field:'t.nom'}},
            {text: 'Prix (€)', value: {name:'Inscription : Prix', field:'t.tarif', number:true}},
            {text: 'Gourde (€)', value: {name:'Inscription : Gourde', field:'p.recharge', number:true}},
            {text: 'Malus (€)', value: {name:'Inscription : Malus', field:'p.malus', number:true}},
            {text: 'Total (€)', value: {name:'Inscription : Total', field:'p.total', number:true}},
            {text: 'Logeur', value: {name:'Inscription : Logeur', field:'p.logeur'}},
            {text: '-'},
            {text: 'Logement (0/1)', value: {name:'Inscription : Logement', field:'t.logement', bool:true}},
            {text: 'Retard (0/1)', value: {name:'Inscription : Retard', field:'p.retard', bool:true}},
            {text: 'Signature (0/1)', value: {name:'Inscription : Signature', field:'p.signature', bool:true}}]},

        {text: 'Chambre', menu: [
            {text: 'Bâtiment', value: {name:'Chambre : Bâtiment', field:'c.batiment'}},
            {text: 'Numéro', value: {name:'Chambre : Numéro', field:'c.numero'}},
            {text: 'Autres filles', value: {name:'Chambre : Autres filles', field:'c.autres'}},
            {text: 'Propriétaire', value: {name:'Chambre : Propriétaire', field:'c.proprio'}}]},
        
        {text: 'Sport(s)', menu: [
            {text: 'Nom', value: {name:'Sport : Nom', field:'s.sport'}},
            {text: 'Equipe', value: {name:'Sport : Equipe', field:'s.equipe'}},
            {text: 'Capitaine', value: {name:'Sport : Capitaine', field:'s.capitaine'}},
            {text: '-'},
            {text: 'Individuel (0/1)', value: {name:'Sport : Individuel', field:'s.individuel', bool:true}},
            {text: 'Sexe (f/h/m)', value: {name:'Sport : Sexe', field:'s.sexe', bool:true}},
            {text: 'Capitaine (0/1)', value: {name:'Sport : Capitaine', field:'s.is_capitaine', bool:true}}]},

        {text: 'École', menu: [
            {text: 'Nom', value: {name:'École : Nom', field:'e.nom'}},
            {text: 'Image', value: {name:'École : Image', field:'e.image', simple:true}},
            {text: 'Malus (%)', value: {name:'École : Malus', field:'e.malus', number:true}},
            {text: '-'},
            {text: 'Lyonnaise (0/1)', value: {name:'École : Lyonnaise', field:'e.ecole_lyonnaise', bool:true}},
            {text: 'Format long (0/1)', value: {name:'École : Format', field:'e.format', bool:true}}]},
        
        {text: 'Respo. Ecole', menu: [
            {text: 'Prénom', value: {name:'Respo. École : Prénom', field:'r.prenom'}},
            {text: 'Prénom / Nom', value: {name:'Respo. École : Prénom / Nom', field:'r.prenomnom'}},
            {text: 'Email', value: {name:'Respo. École : Email', field:'r.email'}},
            {text: 'Téléphone', value: {name:'Respo. École : Téléphone', field:'r.telephone'}}]},

        {text: 'Respo. Sport', menu: [
            {text: 'Prénom', value: {name:'Respo. Sport : Prénom', field:'rs.prenom'}},
            {text: 'Prénom / Nom', value: {name:'Respo. Sport : Prénom Nom', field:'rs.prenomnom'}},
            {text: 'Email', value: {name:'Respo. Sport : Email', field:'rs.email'}},
            {text: 'Téléphone', value: {name:'Respo. Sport : Téléphone', field:'rs.telephone'}}]},
        
        {text: '-'},

        {text: 'Spécial', menu: [
            {text: 'Heure', value: {name:'Spécial : Heure', field:'@heure', special:true}},
            {text: 'Date',  value: {name:'Spécial : Date', field:'@date', special:true}},
            {text: 'Année', value: {name:'Spécial : Année', field:'@annee', special:true}},
            {text: 'Édition', value: {name:'Spécial : Édition', field:'@edition', special:true}},
            {text: 'URL', value: {name:'Spécial : URL', field:'@url', special:true}},
            {text: 'URL appli', value: {name:'Spécial : URL appli', field:'@url_app', special:true}},
        ]},

        {text: 'Condition', menu: [
            {text: 'Existence', value: {cond:null}},
            {text: 'Egalité =', value: {cond:true}},
            {text: 'Différence ≠', value: {cond:false}}]}];

    var challengerFormats = [
            { title: 'Orange Challenge', inline: 'span', styles: { color: '#ff8300' } },
            { title: 'Bleu Challenge', inline: 'span', styles: { color: '#0032a0' } },
            { title: '-'},
            { title: 'Paragraphe', block: 'p', styles : {} },
            { title: 'Bouton', block: 'div', styles: { 'background-color': 'orange', padding: '10px 30px', color: '#FFF', width: '200px', margin:'10px auto'} },
            { title: 'Titre', block: 'h1' },
            { title: 'Sous-titre', block: 'h3' }];

    var challengerStyle = 
        "span[data-space] { display:inline-block; vertical-align:middle; overflow:hidden; width:5px !important; height:5px !important; outline:0 !important; border:1px dotted black; background-color:rgba(200, 200, 200, 0.75); padding:0; border-radius:3px; } " + 
        "span[data-challenger] { display:inline-block; margin:0 -0px !important; outline:0 !important; } " + 
        "span[data-challenger]:before { content:attr(data-name); border:1px dotted black; background-color:rgba(200, 200, 200, 0.75); padding:0; border-radius:3px } " +
        "span[data-challenger][data-field]:before { border-color:orange; background-color:rgba(255, 200, 0, 0.75); } " +
        "span[data-challenger][data-cond]:before { border-color:blue; background-color:rgba(100, 100, 255, 0.75); padding:0 3px; } " + 
        "span[data-challenger][data-bool]:before { border-color:green; background-color:rgba(100, 255, 100, 0.75); } " + 
        "span[data-challenger][data-number]:before { border-color:red; background-color:rgba(255, 100, 100, 0.75); } " + 
        "span[data-challenger][data-special]:before { border-color:pink; background-color:rgba(200, 100, 200, 0.75); } " + 
        "span:not([data-cond])[style*=underline] span[data-challenger] { text-decoration:underline; } " + 
        "span:not([data-cond])[style*=line-through] span[data-challenger]:before { text-decoration:line-through; } ";

    var challengerCountSms = 'Caractères: {0}';
    var space = '<span data-space contenteditable="false">&nbsp;</span>';
    var style = ' style="challenger:0" data-mce-style="challenger:0"';
    var styleNoDisplay = ' style="challenger:0;display:none" data-mce-style="challenger:0;display:none"';
    var challengerOption = function (e, editor, menu) {
        if (typeof menu.value().cond !== 'undefined') {
            editor.insertContent('<span data-challenger data-cond="if" data-name="SI" contenteditable="false"' + style +'></span>' +
                (menu.value().cond !== null ? '<span data-challenger data-cond="op' + (menu.value().cond ? '1' : '0')+ '" data-name="' + (menu.value().cond ? '=' : '≠') + '" contenteditable="false"' + style +'></span>' 
                    : '<span data-challenger data-cond="op" contenteditable="false"' + styleNoDisplay +'></span>') +
                '<span data-challenger data-cond="then" data-name="ALORS" contenteditable="false"' + style +'></span>' +
                '<span data-challenger data-cond="else" data-name="SINON" contenteditable="false"' + style +'></span>' +
                '<span data-challenger data-cond="fi" data-name="FIN" contenteditable="false"' + style +'></span>');
        } else if (typeof menu.value().simple !== 'undefined') {
            editor.insertContent('{{' + menu.value().field + '}}');
        } else { 
            var bool = typeof menu.value().bool !== 'undefined' ? ' data-bool' : '';
            var number = typeof menu.value().number !== 'undefined' ? ' data-number' : '';
            var special = typeof menu.value().special !== 'undefined' ? ' data-special' : '';
            editor.insertContent(space + '<span data-challenger data-field="' + menu.value().field + '" data-name="' + menu.value().name + '"' + bool + number + special + style +'></span>' + space);
        }

        menu.value(null);
    };

    tinymce.PluginManager.add('charactercount', function (editor) {
        var self = this;

        function update() {
            editor.theme.panel.find('#charactercount').text([challengerCountSms, self.getCount()]);
        }

        editor.on('init', function () {
            var statusbar = editor.theme.panel && editor.theme.panel.find('#statusbar')[0];

            if (statusbar) {
                window.setTimeout(function () {
                    statusbar.insert({
                        type: 'label',
                        name: 'charactercount',
                        text: [challengerCountSms, self.getCount()],
                        classes: 'charactercount',
                        disabled: editor.settings.readonly
                    }, 0);

                    editor.on('setcontent beforeaddundo', update);
                    editor.on('keyup', function (e) {
                        update();
                    });
                }, 0);
            }
        });

        self.getCount = function () {
            var tx = editor.getContent({ format: 'raw' });
            var decoded = decodeHtml(tx);
            var decodedStripped = decoded.replace(/(<([^>]+)>)/ig, "").trim();
            var tc = decodedStripped.length;
            return tc;
        };

        function decodeHtml(html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }
    });

    tinymce.init({
        selector: 'textarea#form-email',
        language: 'fr_FR',
        plugins : 'link image lists anchor textcolor table code fullscreen colorpicker paste',
        menu: {},
        toolbar: [
            'undo redo | styleselect fontselect fontsizeselect | link unlink anchor | image table', 
            'bold italic underline strikethrough | superscript subscript | alignleft aligncenter alignright alignjustify | forecolor backcolor | bullist numlist | outdent indent',
            'challenger | removeformat fullscreen code'
        ],
        resize: true,
        style_formats: challengerFormats,
        font_formats: challengerFonts.join(';'),
        fontsize_formats: challengerSizes.join(' '),
        setup: function (editor) {
            editor.on('init', function() {
                this.getBody().style.fontSize = defautSize;
                this.getBody().style.fontFamily = defaultFont;
            });
            editor.on('change', function() {
                edited = true; //Variable globale
            });
            editor.addButton('challenger', {
                type: 'listbox',
                text: 'Challenger',
                icon: 'nonbreaking',
                onselect: function(e) { challengerOption(e, editor, this); },
                values: challengerMenu
            });
        },
        init_instance_callback : function(editor) {
            editor.setContent($('#' + this.id).val(), {format : 'raw'});
        },
        extended_valid_elements: "span[*]",
        valid_children: "+body[span]",
        content_style: challengerStyle,
        convert_urls: false,
    });



    var config_text = {
        language: 'fr_FR',
        plugins : 'charactercount paste',
        menu: {},
        forced_root_block : false,
        force_p_newlines : false,
        force_br_newlines : true,
        convert_newlines_to_brs : false,
        remove_linebreaks : true, 
        elementpath: false,
        formats: {
            bold: {},
            italic: {},
            underline: {},
            strikethrough: {}
        },
        toolbar: [
            'undo redo | challenger'
        ],
        resize: true,
        setup: function (editor) {
            editor.on('init', function() {
                this.shortcuts.add = function() {};
                this.getBody().style.fontSize = defautSize;
                this.getBody().style.fontFamily = defaultFont;
            });
            editor.on('change', function() {
                edited = true; //Variable globale
            });
            editor.addButton('challenger', {
                type: 'listbox',
                text: 'Challenger',
                icon: 'nonbreaking',
                onselect: function(e) { challengerOption(e, editor, this); },
                values: challengerMenu
            });
        },
        init_instance_callback : function(editor) {
            editor.setContent($('#' + this.id).val(), {format : 'raw'});
        },
        invalid_elements: "*[*]",
        valid_elements: "span[*|style:],br[style:],p[style:]",
        valid_children: "+body[span]",
        content_style: "p { margin:0; padding:0; } " + challengerStyle,
        convert_urls: false,
    };

    var config_sms = Object.assign({}, config_text);
    config_sms.selector = 'textarea#form-sms';
    tinymce.init(config_sms);

    var config_titre = Object.assign({}, config_text);
    config_titre.selector = 'input#form-titre';
    config_titre.statusbar = false;
    config_titre.height = '4em';
    config_titre.plugins = 'paste';
    tinymce.init(config_titre); 
});