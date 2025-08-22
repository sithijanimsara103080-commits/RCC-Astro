<?php
// Define the path to your data.json file.
// Assuming this posts_section.php file is in New folder/,
// and 'uploads' is also in New folder/.
$dataFile = "C:\Users\MSI\Desktop\New folder\uploads/data.json";
$data = [];

if (file_exists($dataFile)) {
    $dataJson = file_get_contents($dataFile);
    $data = json_decode($dataJson, true);
    // Ensure data is an array even if json_decode fails or returns null
    if (!is_array($data)) {
        $data = [];
    }
}

if (!empty($data)) {
    $data = array_reverse($data);
}

// No HTML <head>, <body>, <h1>, or <footer> tags here, as this file is meant to be included.
// The styling should be in the main HTML (index.php) or an external CSS file.
?>
<style>
  /* Basic styling for the gallery items, can be integrated into your existing style.css */
  .astronomy-posts {
    display: flex; /* Use flex for horizontal scrolling */
    flex-wrap: nowrap; /* Prevent wrapping to new lines */
    overflow-x: auto; /* Enable horizontal scrolling */
    gap: 1em; /* Space between items */
    padding: 1em 0; /* Some padding */
    scroll-behavior: smooth; /* Smooth scrolling */
    -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
  }
  .astronomy-post {
    flex: 0 0 auto; /* Do not grow, do not shrink, base on content width */
    width: 250px; /* Fixed width for each post */
    background: #222;
    border-radius: 8px;
    padding: 0.5em;
    position: relative;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
  }
  .astronomy-post img {
    max-width: 100%;
    border-radius: 8px;
    height: 150px; /* Fixed height for images */
    object-fit: cover; /* Crop images to fit */
    margin-bottom: 0.5em;
  }
  .astronomy-post h3 {
    margin: 0 0 0.3em 0;
    color: #eee;
  }
  .astronomy-post p {
    margin: 0 0 0.5em 0;
    font-size: 0.9em;
    color: #ccc;
    flex-grow: 1; /* Allow description to take up available space */
  }
  .astronomy-post a {
    display: inline-block;
    margin-top: 0.5em;
    padding: 0.3em 0.6em;
    background: #0af;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.9em;
  }
  .astronomy-posts-container {
    position: relative;
    display: flex;
    align-items: center;
  }
  .scroll-arrow {
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    position: absolute;
    z-index: 10;
    font-size: 1.5em;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .scroll-arrow.left {
    left: -20px;
  }
  .scroll-arrow.right {
    right: -20px;
  }
</style>

<?php if (!empty($data)): ?>
    <?php foreach ($data as $item): ?>
        <div class="astronomy-post">
            <img src="../uploads/<?php echo htmlspecialchars($item['filename']); ?>" 
                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                 loading="lazy" />
            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
            <p><?php echo htmlspecialchars($item['description']); ?></p>
            <?php if (!empty($item['link'])): ?>
                <a href="<?php echo htmlspecialchars($item['link']); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer">
                    <i class="fas fa-external-link-alt"></i> Read More
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="astronomy-post empty-state">
        <div class="empty-icon">🌟</div>
        <h3>No Posts Yet</h3>
        <p>Check back soon for exciting astronomy content!</p>
        <a href="#contact" class="btn">Get in Touch</a>
    </div>
<?php endif; ?>

<script>
  // Re-add the scroll function as it's still needed for the arrows
  function scrollPosts(scrollAmount) {
    const astronomyPosts = document.getElementById('astronomyPosts');
    astronomyPosts.scrollBy({
      left: scrollAmount,
      behavior: 'smooth'
    });
  }
</script>