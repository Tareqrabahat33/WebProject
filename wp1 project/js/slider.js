var sliderImages = document.querySelectorAll('.slider-img');
var currentImageIndex = 0;

function changeSlide(n) {
    currentImageIndex += n;

    if (currentImageIndex < 0) {
        currentImageIndex = sliderImages.length - 1;
    } else if (currentImageIndex >= sliderImages.length) {
        currentImageIndex = 0;
    }

    for (var i = 0; i < sliderImages.length; i++) {
        sliderImages[i].style.display = 'none';
    }

    sliderImages[currentImageIndex].style.display = 'block';
}

function startSlider() {
    setInterval(function() {
        changeSlide(1);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    for (var i = 0; i < sliderImages.length; i++) {
        sliderImages[i].style.display = 'none';
    }
    sliderImages[0].style.display = 'block';
    
    startSlider();
});
