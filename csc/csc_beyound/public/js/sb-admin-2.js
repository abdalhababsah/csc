//
// Global init of core components
//
function addModal(id, route, title = 'Add', form_id, table_id, table_type = false) {
  $('#' + id).on('click', function () {
      $.ajax({
          url: route,
          method: 'get',
          success: function (data) {
              $('#modal-body').html(data);
              $('#modal-title').text(title);
              $('#modal').modal('show');

              $('#' + form_id).submit(function (e) {
                  $(".span_error").each(function () {
                      $(this).remove()
                  });
                  e.preventDefault();
                  $("#btn-submit").prop("disabled", false)
                  var form = $(this);
                  var url = form.attr('action');
                  $.ajax({
                      type: "POST",
                      url: url,
                      data: new FormData(this),
                      dataType: "json",
                      contentType: false,
                      cache: false,
                      processData: false,
                      success: function (data) {
                          if (data.status === 422) {
                              $("#btn-submit").prop("disabled", false)
                              $.each(data.errors, function (index, value) {
                                  console.log(index)
                                  var error = '<span class="text-danger span_error"> ' + value + '</span>'
                                  $('[name="' + index + '"]').parent().last().append(error)
                              });
                              Swal.fire({
                                  icon: 'error',
                                  title: 'Oops,there were an errors...',
                              })
                          } else {
                              Swal.fire({
                                  icon: 'success',
                                  title: data.message,
                                  showConfirmButton: false,
                                  timer: 1500
                              });
                              if (table_type)
                                  table_id.ajax.reload();
                              else
                                  window.LaravelDataTables[table_id].ajax.reload();
                              $('#modal').modal('hide');
                              $('#modal-body').empty()
                          }
                      }
                  });

              });
          }
      });
  });
}

function editModal(editClass, route, title = 'Edit', form_id, table_id) {
  $(document).on('click', '.' + editClass, function () {
      var id = $(this).attr('id');
      $.ajax({
          url: '/' + route + '/' + id + '/edit',
          method: 'get',
          success: function (data) {
              $('#modal-body').html(data);
              $('#modal-title').text(title);
              $('#modal').modal('show');

              $('#' + form_id).submit(function (e) {
                  $(".span_error").each(function () {
                      $(this).remove()
                  });
                  e.preventDefault();
                  $("#btn-submit").prop("disabled", false)
                  var form = $(this);
                  var url = form.attr('action');
                  $.ajax({
                      type: "POST",
                      url: url,
                      data: new FormData(this),
                      dataType: "json",
                      contentType: false,
                      cache: false,
                      processData: false,
                      success: function (data) {
                          if (data.status === 422) {
                              $("#btn-submit").prop("disabled", false)
                              $.each(data.errors, function (index, value) {
                                  console.log(index)
                                  var error = '<span class="text-danger span_error"> ' + value + '</span>'
                                  $('[name="' + index + '"]').parent().last().append(error)
                              });
                              Swal.fire({
                                  icon: 'error',
                                  title: 'Oops,there were an errors...',
                              })
                          } else {
                              Swal.fire({
                                  icon: 'success',
                                  title: data.message,
                                  showConfirmButton: false,
                                  timer: 1500
                              });
                              window.LaravelDataTables[table_id].ajax.reload();
                              $('#modal').modal('hide');
                              $('#modal-body').empty()
                          }
                      }
                  });

              });
          }
      });
  });
}

function remove(removeClass, url, table_id, csrf_token, table_type = false) {
  $(document).on('click', '.' + removeClass, function () {
      var id = $(this).attr('id');
      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then(function (result) {
          if (result.value) {
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': csrf_token
                  },
                  url: '/' + url + '/' + id,
                  method: 'delete',
                  success: function (data) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Your item has been removed',
                          showConfirmButton: false,
                          timer: 1500
                      });
                      if (table_type)
                          table_id.ajax.reload();
                      else
                          window.LaravelDataTables[table_id].ajax.reload();
                  }
              });
          }
      });

  });
}

$('#btn_model_close').on('click', function () {
  $('#modal-body').empty()
});





(function($) {
  "use strict"; // Start of use strict

  // Toggle the side navigation
  $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
    $("body").toggleClass("sidebar-toggled");
    $(".sidebar").toggleClass("toggled");
    if ($(".sidebar").hasClass("toggled")) {
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // Close any open menu accordions when window is resized below 768px
  $(window).resize(function() {
    if ($(window).width() < 768) {
      $('.sidebar .collapse').collapse('hide');
    };
    
    // Toggle the side navigation when window is resized below 480px
    if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
      $("body").addClass("sidebar-toggled");
      $(".sidebar").addClass("toggled");
      $('.sidebar .collapse').collapse('hide');
    };
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // Scroll to top button appear
  $(document).on('scroll', function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on('click', 'a.scroll-to-top', function(e) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    e.preventDefault();
  });

})(jQuery); // End of use strict
