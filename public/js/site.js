id('month-switcher-month').onchange = function() {
    id('month-switcher-form').submit();
};

id('month-switcher-year').onchange = function() {
    id('month-switcher-form').submit();
};

if (id('patient-name') !== null) {
    id('patient-name').onfocus = function() {
        if (this.value == 'Nom du patient')
            this.value = '';
    };
    id('patient-name').onblur = function() {
        if (this.value == '')
            this.value = 'Nom du patient';
    };
}
if (id('amount') !== null) {
    id('amount').onfocus = function() {
        if (this.value == 'Montant')
            this.value = '';
    };
    id('amount').onblur = function() {
        if (this.value == '')
            this.value = 'Montant';
    };
}
if (id('name') !== null) {
    id('name').onfocus = function() {
        if (this.value == 'Nom')
            this.value = '';
    };
    id('name').onblur = function() {
        if (this.value == '')
            this.value = 'Nom';
    };
}

if (id('message')) {
    setTimeout('hideMessage()', 2000);
}