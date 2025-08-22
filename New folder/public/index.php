<!DOCTYPE html>
<html lang="en">
  <head>
    <meta property="og:title" content="Welcome to Our Astronomy Society" />
    <meta property="og:description" content="Join us in exploring the wonders of the universe. Our society brings together astronomy enthusiasts for events, discussions, and more." />
    <meta property="og:image" content="https://images.pixieset.com/72717209/3fdf7604f24181001a8519224cc4c32c-large.png" />
    <meta property="og:url" content="http://rccastro.kesug.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="RCC astro Astronomy Society" />
    <title>Welcome to Our Astronomy Society</title>
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Welcome to Our Astronomy Society" />
    <meta name="twitter:description" content="Join us in exploring the wonders of the universe." />
    <meta name="twitter:image" content="https://images.pixieset.com/72717209/3fdf7604f24181001a8519224cc4c32c-large.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.11/dist/css/splide.min.css">
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-85417367-1"></script><script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.11/dist/js/splide.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://images.pixieset.com/72717209/02df3169dc9ca2440c9490dc6c590180-large.png">
    <title>Astronomy Society</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  </head>
<body>

<header class="header">
  <div class="container">
    <div class="nav">
      <div>
        <a class="logo" href="#hero">RAS</a>
      </div>
      

<button class="hamburger" aria-label="Open menu" aria-expanded="false" onclick="toggleMenu(this)">
  ☰
</button>

<ul class="nav-list" id="nav-list">
  <li><a href="#hero">Home</a></li>
  <li><a href="#about">About</a></li>
  <li><a href="#events">Events</a></li>
  <li><a href="#gallery">Gallery</a></li>
  <li><a href="#contact">Contact</a></li>
  
  <li class="dropdown">
    <a href="#" class="dropbtn">Members <i class="fas fa-chevron-down"></i></a>
    <ul class="dropdown-content">
      <li><a href="#">President - Chanul Chathmal</a></li>
      <li><a href="#">Vice President - Nadija Indeewara</a></li>
      <li><a href="#">Web Developer - Sithija Nimsara</a></li>
    </ul>
  </li>

  <li class="dropdown">
    <a href="#" class="dropbtn">Teachers <i class="fas fa-chevron-down"></i></a>
    <ul class="dropdown-content">
      <li><a href="#">Principal - Chandika Deshapriya</a></li>
      <li><a href="#">Teacher-In-Charge - Aruna Wicramanayaka</a></li>
      <li><a href="#">Teacher-In-Charge - Sammani Pathirana</a></li>
      <li><a href="#">Teacher-In-Charge - Hashini Madhushika</a></li>
    </ul>
  </li>
</ul>
    </div>
  </div>
</header>

<script>
  function toggleMenu(button) {
    const navList = document.querySelector('.nav-list');
    const isExpanded = button.getAttribute('aria-expanded') === 'true';

    navList.classList.toggle('active');
    button.setAttribute('aria-expanded', !isExpanded);
    button.classList.toggle('active'); // Add/remove 'active' class on the hamburger button
  }

  // Optional: Close menu when a link inside is clicked (for single-page navigation)
  document.querySelectorAll('.nav-list a').forEach(link => {
    link.addEventListener('click', () => {
      const navList = document.querySelector('.nav-list');
      const hamburgerButton = document.querySelector('.hamburger');
      if (navList.classList.contains('active')) {
        navList.classList.remove('active');
        hamburgerButton.classList.remove('active');
        hamburgerButton.setAttribute('aria-expanded', 'false');
      }
    });
  });

  // Handle dropdown toggle for mobile
  document.querySelectorAll('.nav-list .dropdown > .dropbtn').forEach(dropbtn => {
    dropbtn.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent default link behavior
      const parentDropdown = dropbtn.closest('.dropdown');
      // Toggle active class on the parent dropdown
      parentDropdown.classList.toggle('active');
      // Optionally close other open dropdowns
      document.querySelectorAll('.nav-list .dropdown.active').forEach(openDropdown => {
        if (openDropdown !== parentDropdown) {
          openDropdown.classList.remove('active');
        }
      });
    });
  });

</script>

  <section id="hero" class="hero-section animate fade-in">
    <div class="container hero-content">
      <h2>Welcome to Our Astronomy Society</h2>
      <p>Join us in exploring the wonders of the night sky and unraveling the mysteries of the cosmos.</p>
      <div class="hero-buttons">
        <a href="#about" class="btn primary-btn">Learn More</a>
        <a href="https://wa.me/+94763146775" class="btn secondary-btn">Join</a>
      </div>
    </div>
  </section>

  <section id="about" class="about-section animate slide-in-left">
    <div class="container">
      <h2>About Us</h2>
      <p>Our Radapasa Astronomy Society, which started in 2013, is now approaching 12 years. Currently, we are working as one of the most active astronomy societies. Astronomy workshops and night sky observation camps were continuously held for the junior members of the Astronomy Society.Apart from that, we have many achievements in the National Astronomy Olympiad held every year and international level also.This is an another step of ours. We started this web page to provide knowledge about astronomy not only to the students of our school but also to our own brothers and sisters in Sri Lanka.</p>
    </div>
  </section>

  <section class="astronomy-section animate slide-in-left">
    <div class="container">
      <h2 class="section-title">
        <span class="title-icon"></span>
        Our Posts & Explore
        <span class="title-icon"></span>
      </h2>
      <p class="section-subtitle">Discover the latest astronomy insights and cosmic wonders</p>
      
      <div class="astronomy-posts-container">
        <button class="scroll-arrow left" onclick="scrollPosts(-300)" aria-label="Scroll left">
          <i class="fas fa-chevron-left"></i>
        </button>
        
        <div class="astronomy-posts" id="astronomyPosts">
          <?php include 'C:\Users\MSI\Desktop\New folder\uploads\posts_section.php'; ?>
        </div>
        
        <button class="scroll-arrow right" onclick="scrollPosts(300)" aria-label="Scroll right">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      
      <div class="posts-indicators" id="postsIndicators">
        <!-- Dynamic indicators will be added by JavaScript -->
      </div>
    </div>
  </section>

<section id="events" class="events-section animate slide-up">
  <div class="container">
    <h2>YOU ARE INVITED TO <span class="highlight">AURORA AETHERION</span></h2>
    <div class="events-list">
      <article class="event" data-date="2025-08-29T15:00:00+05:30">
        <h3>The Official Badge Wearing Ceremony & Night Sky Observation Camp</h3>
        <p class="details">
          Organized by the <strong>Weeraketiya Rajapaksa Central College Astronomical Society</strong>, 
          to be held at the <strong>College Auditorium</strong>.
        </p>
        <div class="event-date">
          <p><strong>August</strong></p>
          <p><strong>Friday</strong></p>
          <p class="day">29</p>
          <p>3:00 P.M.</p>
          <p>2025</p>
        </div>

        <!-- Countdown Box -->
        <p class="countdown">
          Countdown: <span></span>
        </p>

        <p class="signature">– Radapasa Astronomical Society –</p>
      </article>
    </div>
  </div>
</section>

<script>
function updateCountdowns() {
  const events = document.querySelectorAll(".event");

  events.forEach(event => {
    const dateStr = event.getAttribute("data-date");
    const eventDate = new Date(dateStr).getTime();
    const now = new Date().getTime();
    const timeLeft = eventDate - now;

    const countdownEl = event.querySelector(".countdown span");

    if (timeLeft <= 0) {
      countdownEl.innerText = "Event Started 🎉";
      return;
    }

    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

    countdownEl.innerText = `${days}d ${hours}h ${minutes}m ${seconds}s`;
  });
}

setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>

  <section id="gallery" class="gallery-section animate slide-up">
    <div class="container">
      <h2 class="section-title">
        <span class="title-icon"></span>
        Photo Gallery
        <span class="title-icon"></span>
      </h2>
      <p class="section-subtitle">Explore our astronomy adventures and achievements</p>
      
      <div class="gallery-controls">
        <button class="gallery-filter active" data-filter="all">All</button>
        <button class="gallery-filter" data-filter="competitions">Competitions</button>
        <button class="gallery-filter" data-filter="events">Events</button>
      </div>
      
      <div class="gallery-grid" id="galleryGrid">
        <div class="gallery-item" data-category="competitions">
          <div class="gallery-item-inner">
            <img src="https://images.pixieset.com/18849209/e62cade1740f3d1bfdd07aa4c1a856fc-xxlarge.png" 
                 alt="AUDAX ETHERION All Island Astronomy Competition"
                 loading="lazy">
            <div class="gallery-overlay">
              <h3>AUDAX ETHERION All Island Astronomy Competition</h3>
              <p>National level astronomy competition showcasing young talent</p>
              <a href="AUDAX ETHERION All Island Astronomy Competition.html" class="gallery-btn">
                <i class="fas fa-external-link-alt"></i> View Details
              </a>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="competitions">
          <div class="gallery-item-inner">
            <img src="https://images.pixieset.com/11607209/f99849b2502e9a8b6ec9c899e2d03e9b-xxlarge.jpg" 
                 alt="Appreciation of Sri Lankan Junior Astronomy Olympiad Competition 2023"
                 loading="lazy">
            <div class="gallery-overlay">
              <h3>Junior Astronomy Olympiad Competition 2023</h3>
              <p>Recognition for outstanding performance in national competition</p>
              <a href="Appreciation of Sri Lankan Junior Astronomy Olympiad Competition 2023.html" class="gallery-btn">
                <i class="fas fa-external-link-alt"></i> View Details
              </a>
            </div>
          </div>
        </div>
        
        <div class="gallery-item" data-category="competitions">
          <div class="gallery-item-inner">
            <img src="https://images.pixieset.com/72717209/8e22653981c93697289e5f3c21426daf-xxlarge.png" 
                 alt="Appreciation of International Astronomy and Astrophysics Olympiad 2024"
                 loading="lazy">
            <div class="gallery-overlay">
              <h3>International Astronomy Olympiad 2024</h3>
              <p>Global recognition for excellence in astronomy and astrophysics</p>
              <a href="https://rccastro.pixieset.com/appreciationofinternationalastronomyandastrophysicsolympiad2024/" 
                 class="gallery-btn" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Details
              </a>
            </div>
          </div>
        </div>
        
      <div class="gallery-load-more">
        <button class="load-more-btn" id="loadMoreBtn">
          <i class="fas fa-plus"></i> Load More Photos
        </button>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const animatedElements = document.querySelectorAll(".animate");

      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
          } else {
            entry.target.classList.remove("visible"); // Reset when out of view
          }
        });
      }, { threshold: 0.3 });

      animatedElements.forEach(el => observer.observe(el));
    });
  </script>

  <style>
    /* Fade In Animation */
    .fade-in { opacity: 0; transition: opacity 1s ease-in-out; }
    .fade-in.visible { opacity: 1; }

    /* Slide In Animation */
    .slide-in-left { transform: translateX(-50px); opacity: 0; transition: all 1s ease-in-out; }
    .slide-in-left.visible { transform: translateX(0); opacity: 1; }

    /* Button Hover Effect */
    .btn:hover { transform: scale(1.05); transition: transform 1.3s ease; }

    /* Gallery Zoom Effect */
    .gallery-item img { transition: transform 0.3s ease; }
    .gallery-item img:hover { transform: scale(1.1); }
  </style>

<section id="contact">
  <div class="contact-container">
    <h2>Contact Us</h2>
    <p>We'd love to hear from you! Feel free to reach out using the form below.</p>

<form action="process_contact.php" method="POST" class="contact-form">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Your Name" required>
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Your Email" required>
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" placeholder="Your Message" rows="5" required></textarea>
      </div>

       <button type="submit" class="submit-btn">Send Message</button>

    </form>
    <div class="contact-info">
  <p><strong>Email:</strong> <a href="mailto:Astronomyrcc@yahoo.com">Astronomyrcc@yahoo.com</a></p>
  <p><strong>Phone:</strong> <a href="tel:+94763146775">+94763146775</a></p>
  <p><strong>Address:</strong> Rajapaksa Central College, Tangalle Road, Weeraketiya</p>
  <p><strong>Location:</strong></p>
  <div class="map-container">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.974706683914!2d80.7679854741803!3d6.134100727567664!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae14db376410fb1%3A0x962895e28f03c339!2zUmFqYXBha3NhIENlbnRyYWwgQ29sbGVnZSAtIFdlZXJha2V0aXlhIHwg4La74LeP4Lai4La04Laa4LeK4LeCIOC2uOC2sOC3iuKAjeC2uiDgt4Dgt5Lgtq_gt4rigI3gtrrgt4_gtr3gtrogLSDgt4Dgt5Pgtrvgtprgt5Dgtqfgt5Lgtro!5e0!3m2!1sen!2slk!4v1739468686884!5m2!1sen!2slk"
      width="100%"
      height="300"
      style="border:0;"
      allowfullscreen=""
      loading="lazy">
    </iframe>
  </div>
</div>
<style>
  /* Basic Responsive Styling */
  .contact-info {
    font-family: Arial, sans-serif;
    padding: 20px;
    max-width: 600px;
    margin: 0 auto;
  }

  .contact-info p {
    margin: 10px 0;
    font-size: 16px;
  }

  .contact-info a {
    color: #ffffff;
    text-decoration: none;
  }

  .map-container {
    position: relative;
    overflow: hidden;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
  }

  .map-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
  }

  @media (max-width: 600px) {
    .contact-info {
      padding: 10px;
    }

    .contact-info p {
      font-size: 14px;
    }

    .map-container {
      padding-top: 75%; /* Adjust for smaller screens */
    }
  }
</style>
</section>

    <div class="social-icons">
      <a href="#" target="_blank" class="social-icon facebook" title="Follow us on Facebook">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a href="#" target="_blank" class="social-icon twitter" title="Follow us on Twitter">
        <i class="fab fa-twitter"></i>
      </a>
      <a href="#" target="_blank" class="social-icon instagram" title="Follow us on Instagram">
        <i class="fab fa-instagram"></i>
      </a>
      <a href="#" target="_blank" class="social-icon linkedin" title="Connect with us on LinkedIn">
        <i class="fab fa-linkedin-in"></i>
      </a>
    </div>
  </div>
</section>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script src="script.js"></script>

<footer>
  <p>© 2025 School Astronomy Club. All rights reserved.</p>
</footer>

<style>
      footer {
      margin-top: 30px;
      text-align: center;
      color: #bbb;
    }
</style>

</body>
</html>