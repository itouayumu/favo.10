let slideIndex = 1;
showSlides(slideIndex);

// 次/前のスライドに移動
function plusSlides(n) {
    showSlides(slideIndex += n);
}

// 現在のスライドを表示
function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("slide");
    if (n > slides.length) {slideIndex = 1}
    if (n < 1) {slideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slides[slideIndex - 1].style.display = "block";
}

document.addEventListener("DOMContentLoaded", function() {
    showSlides(); // ページが読み込まれてからshowSlidesを実行
});

