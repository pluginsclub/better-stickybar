jQuery(document).ready(function($) {
  // Add a class to the sticky bar when scrolling down
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
      $('#sticky-bar').addClass('sticky');
    } else {
      $('#sticky-bar').removeClass('sticky');
    }
  });

  // Smooth scroll to the top when the sticky bar is clicked
  $('#sticky-bar').click(function() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
  });
});


window.addEventListener('scroll', function() {
    var scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
    var scrollTop = window.scrollY || document.documentElement.scrollTop;
    var progress = (scrollTop / scrollHeight) * 100;
    document.getElementById('progress-line').style.width = progress + '%';
});
