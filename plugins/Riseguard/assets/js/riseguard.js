
function add_ip_validation () {
  $('input.blacklist_ip').each(function () {
    const that = $(this)
    $(this).rules('add', {
      required: true,
      IP4Checker: true,
      remote: {
        url: 'riseguard/isUniqueIpOrEmail/ip',
        type: 'post'
      },
      messages: {
        remote: 'Value already in database'
      }
    })
  })
}

function add_email_validation () {
  $('input.blacklist_email').each(function () {
    $(this).rules('add', {
      required: true,
      email: true,
      remote: {
        url: 'riseguard/isUniqueIpOrEmail/email',
        type: 'post'
      },
      messages: {
        remote: 'Value already in database'
      }
    })
  })
}

// Edit staff expiry date
function editStaffExpiryDate (staffID, expiryDate) {
  $('#staffid').val(staffID)
  $('#staffid').trigger('change')
  $('#expiry_date').val(expiryDate)
  $('#update').val('1');
  $('#addStaffExpiryForm').find('.add').addClass('hide')
    $('#addStaffExpiryForm').find('.update').removeClass('hide')
}
