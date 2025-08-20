/*window.onload = function prevencionDeNombre() {
  nombres = document.getElementById("nombres");
  //Listen for keypress in the firstname input
  nombres.addEventListener("keypress", function(e) {
    char_regex = RegExp('[A-Za-z][A-Za-z]')
    //Prevent the key to be entered if it's not one of the alphabetic
    if(!char_regex.test(e.key)) {
      e.preventDefault();
    }
  })

    apellidos = document.getElementById("apellidos");
  //Listen for keypress in the firstname input
  apellidos.addEventListener("keypress", function(e) {
    char_regex = RegExp('[A-Za-z]{1}[A-Za-z]')
    //Prevent the key to be entered if it's not one of the alphabetic
    if(!char_regex.test(e.key)) {
      e.preventDefault();
    }
  })
}*/

/*!
* Start Bootstrap - Clean Blog v6.0.9 (https://startbootstrap.com/theme/clean-blog)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-clean-blog/blob/master/LICENSE)
*/
window.addEventListener('DOMContentLoaded', () => {
    let scrollPos = 0;
    const mainNav = document.getElementById('mainNav');
    const headerHeight = mainNav.clientHeight;
    window.addEventListener('scroll', function() {
        const currentTop = document.body.getBoundingClientRect().top * -1;
        if ( currentTop < scrollPos) {
            // Scrolling Up
            if (currentTop > 0 && mainNav.classList.contains('is-fixed')) {
                mainNav.classList.add('is-visible');
            } else {
                console.log(123);
                mainNav.classList.remove('is-visible', 'is-fixed');
            }
        } else {
            // Scrolling Down
            mainNav.classList.remove(['is-visible']);
            if (currentTop > headerHeight && !mainNav.classList.contains('is-fixed')) {
                mainNav.classList.add('is-fixed');
            }
        }
        scrollPos = currentTop;
    });
})
