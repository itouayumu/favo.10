document.getElementById('image_1').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview1').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('image_2').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview2').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('image_3').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview3').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('image_4').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview4').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
