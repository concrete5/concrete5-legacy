(function () {
    
    "use strict";
    
    window.ccmValidateBlockForm = function () {
        if ($('.notfound').length > 0 || $('#ccm_googlemap_block_location').val().length === 0) {
            ccm_addError(ccm_t('location-required'));
            $('#ccm_googlemap_block_location').addClass('notfound');
        }
        return false;
    };
    
    window.C5GMaps = {
        
        init: function () {
            if (!C5GMaps.isMapsPresent()) {
                $('head').append($(unescape("%3Cscript src='https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false&callback=C5GMaps.setupAutocomplete' type='text/javascript'%3E%3C/script%3E")));
            } else {
                C5GMaps.setupAutocomplete();
            }
        },
        
        isMapsPresent: function () {
            if (typeof google === 'object'
                    && typeof google.maps === 'object'
                    && typeof google.maps.places === 'object') {
                return true;
            }
            return false;
        },
        
        setupAutocomplete: function () {
            
            var input = (document.getElementById('ccm_googlemap_block_location')),
                autocomplete = new google.maps.places.Autocomplete(input),
                note = document.getElementById('ccm_googlemap_block_note');
            
            input.onchange = function () {
                this.className = 'notfound';
            };
            
            setInterval(function () { $('.pac-container').css('z-index', '2000'); }, 250);
            
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    // Inform the user that the place was not found and return.
                    input.className = 'notfound';
                    note.innerHTML = 'The place you entered could not be found.';
                    return;
                } else {                    
                    document.getElementById('ccm_googlemap_block_latitude').value = place.geometry.location.jb;
                    document.getElementById('ccm_googlemap_block_longitude').value = place.geometry.location.kb;
                    input.className = '';
                    note.innerHTML = '';
                }
                
            });
            
            
        }
    };
    
    window.C5GMaps.init();
    
}());