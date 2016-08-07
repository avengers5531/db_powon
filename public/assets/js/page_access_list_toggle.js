jQuery(function($) {
  var radios = $('input[type=radio][name=access_type]');
  var members_list = $('#member_table');
  radios.change(function() {
    //var members_list = $('#member_table');
    if (this.value == 'E') {
      members_list.addClass('hide');
    } else if (this.value == 'P') {
      members_list.removeClass('hide');
    }
  });

  var everyoneRadio = document.getElementById('everyoneRadio');
  var privateRadio = document.getElementById('privateRadio');
  if (everyoneRadio.checked) {
    members_list.addClass('hide');
  } else if (privateRadio.checked) {
    members_list.removeClass('hide');
  }

});
