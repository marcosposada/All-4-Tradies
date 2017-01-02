Validation.creditCartTypes = Validation.creditCartTypes.merge({
    'EWAY_MAESTROUK': [false, new RegExp('^([0-9]{3}|[0-9]{4})?$'), false],
    'EWAY_DINERSCLUB': [false, new RegExp('^([0-9]{3}|[0-9]{4})?$'), false],
});