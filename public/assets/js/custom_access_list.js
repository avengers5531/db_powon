// To satisy the vague requirement about WHO CAN DO WHAT, these functions are used for the post creation to handle custom access list on a post.
jQuery(function($) {
  // custom permission list.
  var list = [];
  var error_message;
  function appendElement(name, permission, buttonElement) {
    if (name && name.length > 0) {
      buttonElement.setAttribute('disabled', 'disabled');
      duplicate = list.find(function(it) {
        return it.username.toLowerCase() === name.toLowerCase();
      });
      if (duplicate) {
        error_message = duplicate.username + ' is already in the list';
        render();
      } else {
        $.ajax({
          url: '/api/v1/member',
          type: 'get',
          data: {username: name},
          timeout: 5000 // 5 seconds
        })
        .done(function (data, statusText, xhr) {
          if (xhr.status !== 200) {
            error_message = 'Error code ' + xhr.status;
          } else {
            // no error
            error_message = '';
            var item = {
              username: data.username,
              permission: permission,
              member_id: data.member_id
            };
            list.splice(0, 0, item);
            buttonElement.removeAttribute('disabled');
          }
          render();
        })
        .fail(function (xhr) {
          buttonElement.removeAttribute('disabled');
          if (xhr.status === 404)
            error_message = 'User ' + name + ' does not exist.';
          else {
            error_message = 'An error occurred while verifying if user exists.';
          }
          render();
        })
      }
    }
  }

  // Q&D just attach these functions on the global window object
  // That way, they can be called from the generated html in the render function.
  window.removeElement = function removeElement(index) {
    list.splice(index, 1);
    render();
  };

  window.appendCurrent = function (element) {
    appendElement($('#current_username_permission_input').val().trim(), $('#type_permission_input').val(), element);
  };

  function translateCode(permissionCode) {
    switch (permissionCode) {
      case 'C':
        return 'Can comment';
      case 'A':
        return 'Can link and add content';
      case 'V':
        return 'Can View only';
      case 'T':
        return 'Tailored';
      default:
        return permissionCode;
    }
  }

  function render() {
    var parent = $('#customPermissionList');
    if (parent) {
      parent.empty();
      if (error_message && error_message.length > 0) {
        $('<div id="error_msg" class="alert alert-danger">' +
          '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' +
        '<strong>Warning!</strong> ' + error_message +
        '</div>').appendTo('#customPermissionList');
      } else {
        var errorElement = $('#error_msg');
        if (errorElement) {
          errorElement.remove();
        }
      }
      $('<div class="table-responsive">'+
        '<table class="table table-condensed table-striped table-hover">' +
        '<thead>' +
        '<tr>' +
        '<th>username</th>' +
        '<th>access type</th>' +
        '<th>action</th>' +
        '</tr>' +
        '</thead>' +
        '<tbody id="customUsersList">' +
        '</tbody>' +
        '</table>'+
        '</div>').appendTo('#customPermissionList');

      $('<tr id="first_row">' +
        '<th><input id="current_username_permission_input" type="text"/></th>' +
        '<th><select id="type_permission_input">' +
        '<option value="A" selected="selected">View and add content</option>' +
        '<option value="V">View Only</option>' +
        '<option value="C">View and comment</option>' +
        '</select>' +
        '</th>' +
        '</tr>').appendTo('#customUsersList');

      $('<th><button type="button" onclick="appendCurrent(this)" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-plus"</button></th>').appendTo('#first_row');

      list.forEach(function (item, idx) {
        $('<tr>' +
          '<th>' + item.username + '</th>' +
          '<th>' + translateCode(item.permission) + '</th>' +
          '<th><button class="btn btn-default btn-sm" onclick="removeElement(' + idx + ')"><span class="glyphicon glyphicon-minus"</button></th>' +
          '</tr>').appendTo('#customUsersList');
      });
    }
  }
  function itemSelected() {
    var select_element = $('#select_post_access').val();
    if (select_element === 'T') { // tailored element.
      $('<div class="form-group" id="customPermissionList"></div>').appendTo('#post_access_modal_body');
    } else {
      // delete the div containing table of user custom access
      var divElement = $('#customPermissionList');
      if (divElement) {
        divElement.remove();
      }
    }
    render();
  }
  // start running script here
  $('#select_post_access').change(itemSelected);
  itemSelected();
});
