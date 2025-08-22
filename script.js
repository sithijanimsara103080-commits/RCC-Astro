// Enhanced JavaScript for Astronomy Society Website

document.addEventListener('DOMContentLoaded', function() {
    initPostsSection();
    initGallerySection();
    initMobileMenu();
});

// Posts Section Functionality
function initPostsSection() {
    const postsContainer = document.getElementById('astronomyPosts');
    if (!postsContainer) return;
    
    createPostsIndicators();
    postsContainer.addEventListener('scroll', updatePostsIndicators);
}

function createPostsIndicators() {
    const postsContainer = document.getElementById('astronomyPosts');
    const indicatorsContainer = document.getElementById('postsIndicators');
    
    if (!postsContainer || !indicatorsContainer) return;
    
    const posts = postsContainer.querySelectorAll('.astronomy-post');
    const totalPosts = posts.length;
    const visiblePosts = Math.ceil(postsContainer.clientWidth / 320);
    const totalIndicators = Math.ceil(totalPosts / visiblePosts);
    
    indicatorsContainer.innerHTML = '';
    
    for (let i = 0; i < totalIndicators; i++) {
        const indicator = document.createElement('div');
        indicator.className = 'indicator';
        if (i === 0) indicator.classList.add('active');
        
        indicator.addEventListener('click', () => {
            const scrollPosition = i * visiblePosts * 320;
            postsContainer.scrollTo({
                left: scrollPosition,
                behavior: 'smooth'
            });
        });
        
        indicatorsContainer.appendChild(indicator);
    }
}

function updatePostsIndicators() {
    const postsContainer = document.getElementById('astronomyPosts');
    const indicators = document.querySelectorAll('.indicator');
    
    if (!postsContainer || indicators.length === 0) return;
    
    const scrollPosition = postsContainer.scrollLeft;
    const postWidth = 320;
    const currentIndex = Math.round(scrollPosition / postWidth);
    
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentIndex);
    });
}

// Gallery Section Functionality
function initGallerySection() {
    const filterButtons = document.querySelectorAll('.gallery-filter');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    // Filter functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.getAttribute('data-filter');
            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            galleryItems.forEach(item => {
                const category = item.getAttribute('data-category');
                if (filter === 'all' || category === filter) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeInUp 0.6s ease forwards';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Load more functionality
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMorePhotos);
    }
}

function loadMorePhotos() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const galleryGrid = document.getElementById('galleryGrid');
    
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    loadMoreBtn.disabled = true;
    
    setTimeout(() => {
        loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Load More Photos';
        loadMoreBtn.disabled = false;
    }, 1500);
}

// Mobile Menu Functionality
function initMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navList = document.querySelector('.nav-list');
    
    if (hamburger && navList) {
        hamburger.addEventListener('click', () => {
            const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
            navList.classList.toggle('active');
            hamburger.setAttribute('aria-expanded', !isExpanded);
            hamburger.classList.toggle('active');
        });
        
        document.querySelectorAll('.nav-list a').forEach(link => {
            link.addEventListener('click', () => {
                navList.classList.remove('active');
                hamburger.setAttribute('aria-expanded', 'false');
                hamburger.classList.remove('active');
            });
        });
    }
}

// Posts scrolling function
function scrollPosts(scrollAmount) {
    const postsContainer = document.getElementById('astronomyPosts');
    if (postsContainer) {
        postsContainer.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-in { animation: fadeInUp 0.6s ease forwards; }
`;
document.head.appendChild(style);

