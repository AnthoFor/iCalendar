let menuOpened = 0;
//affichage et desaffichage du menu
hamburger.addEventListener('click', (e) => {
    if (menuOpened == 0) {
        menuOpened = 1;
        hamburger.setAttribute('src', 'public/assets/img/hamburgerClose.svg');
        mobileMenu.classList.add('mobileUp');
        mobileMenu.classList.remove('mobileDown');
    } else if (menuOpened == 1) {
        menuOpened = 0;
        hamburger.setAttribute('src', 'public/assets/img/hamburger.svg');
        mobileMenu.style.width = '0vw';
        mobileMenu.classList.add('mobileDown');
        mobileMenu.classList.remove('mobileUp');
    }    
});