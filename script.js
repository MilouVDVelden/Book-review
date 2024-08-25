const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {container.classList.add("active");

})
loginBtn.addEventListener('click', () => {container.classList.remove("active");
    
})
document.getElementById('profilePic').onclick = function() {
    document.getElementById('fileInput').click();
};

document.getElementById('fileInput').onchange = function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profilePic').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
};

document.getElementById('profileForm').onsubmit = function(event) {
    event.preventDefault();
    alert("Profielinformatie opgeslagen!");
};

function navigateToHome() {
    window.location.href = 'home.html';
}


document.addEventListener('DOMContentLoaded', function() {
    const profilePic = document.getElementById('profilePic');
    const fileInput = document.getElementById('fileInput');

    // Bij het laden van de pagina, controleer of er een afbeelding is opgeslagen in localStorage
    const savedPic = localStorage.getItem('profilePic');
    if (savedPic) {
        profilePic.src = savedPic;
    }

    // Event listener om het bestandselectievenster te openen wanneer de profielfoto wordt geklikt
    profilePic.addEventListener('click', function() {
        fileInput.click();
    });

    // Event listener voor bestandselectie
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imageDataURL = e.target.result;
                profilePic.src = imageDataURL;
                localStorage.setItem('profilePic', imageDataURL);
            };
            reader.readAsDataURL(file);
        }
    });
});
