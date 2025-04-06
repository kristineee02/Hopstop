document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        
    const searchForm = document.querySelector('.search-form form');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const from = document.getElementById('from').value;
        const to = document.getElementById('to').value;
        
        if (!from || !to ) {
            alert('Please fill in all fields');
            return;
        }

        alert(`Searching for buses from ${from} to ${to}`);
    });
    
    const scrollbtn = document.getElementById('scroll-down');
    scrollbtn.addEventListener('click', function() {
        document.querySelector('.hero').scrollIntoView({
            behavior: 'smooth'
        });
    });
});

function login() {
        window.location.href = "../Login/LogIn.php"; 
}