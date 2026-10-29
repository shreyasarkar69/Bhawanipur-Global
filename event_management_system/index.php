<?php
require_once 'db.php';

$sql = "SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id) AS participant_count
        FROM events e ORDER BY e.event_date ASC, e.event_time ASC";
$events = $conn->query($sql);

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function eventImage($title) {
    $t = strtolower((string)$title);
    if (strpos($t, 'cultural') !== false || strpos($t, 'fest') !== false || strpos($t, 'music') !== false || strpos($t, 'dance') !== false)
        return 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=1000&q=85';
    if (strpos($t, 'workshop') !== false || strpos($t, 'tech') !== false || strpos($t, 'web') !== false)
        return 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1000&q=85';
    if (strpos($t, 'sport') !== false || strpos($t, 'game') !== false || strpos($t, 'athletic') !== false)
        return 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1000&q=85';
    return 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=1000&q=85';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#39051b">
  <meta name="description" content="Discover and register for student events at Bhawanipur Global Campus.">
  <title>Campus Connect | Bhawanipur Global Campus</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php">
    <img class="brand-logo" src="assets/bgc-logo.png" alt="Bhawanipur Global Campus BGC logo">
    <span class="brand-copy"><strong>Campus Connect</strong><small>Bhawanipur Global Campus</small></span>
  </a>
  <nav><a class="active" href="index.php">Explore events</a><a href="participants.php">Participant lists</a></nav>
</header>

<main>
  <section class="hero-wrap">
    <div class="hero container">
      <div class="hero-copy">
        <span class="eyebrow light"><span class="live-dot"></span> THE CAMPUS EXPERIENCE</span>
        <h1>Make campus<br>life <span>unforgettable.</span></h1>
        <p>Discover the workshops, cultural moments and competitions that bring our campus together.</p>
        <div class="hero-actions">
          <a class="button button-bright" href="#events">Explore events <span>↘</span></a>
          <a class="text-link" href="participants.php">View participants <span>→</span></a>
        </div>
        <div class="hero-proof"><span class="proof-icon">✦</span><span><strong>Learn. Connect. Celebrate.</strong><small>One campus, countless experiences.</small></span></div>
      </div>
      <div class="hero-art">
        <div class="hero-photo"><img src="assets/campus-building.png" alt="Bhawanipur Global Campus building"></div>
        <div class="float-card float-top"><span class="float-emoji">🎓</span><span><strong>Bhawanipur Global Campus</strong><small>Learn. Grow. Belong.</small></span></div>
        <div class="float-card float-bottom"><span class="float-number">BGC</span><span><strong>Your next memory</strong><small>Starts with showing up.</small></span></div>
        <div class="decor decor-one"></div><div class="decor decor-two"></div>
      </div>
    </div>
    <div class="hero-wave"></div>
  </section>

  <section class="container content-section" id="events">
    <?php if (isset($_GET['registered'])): ?>
      <div class="notice success"><strong>You're on the list!</strong> Registration successful. Your name has been added to the participant list.</div>
    <?php endif; ?>
    <?php if (isset($_GET['duplicate'])): ?>
      <div class="notice warning">This email is already registered for that event.</div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <div class="notice warning"><?= h($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="section-heading">
      <div><p class="eyebrow">FIND YOUR NEXT THING</p><h2>Events worth showing up for<span class="heading-dot">.</span></h2><p class="section-subtitle">A little inspiration for your campus calendar.</p></div>
      <span class="count"><?= $events ? $events->num_rows : 0 ?> upcoming events</span>
    </div>

    <section class="event-grid">
    <?php if ($events && $events->num_rows > 0): ?>
      <?php while ($event = $events->fetch_assoc()): ?>
        <article class="event-card">
          <div class="event-image">
            <img src="<?= h(eventImage($event['title'])) ?>" alt="<?= h($event['title']) ?>" loading="lazy" onerror="this.style.display='none'">
            <span class="image-label">BGC · CAMPUS LIFE</span>
            <div class="event-date"><span><?= h(date('M', strtotime($event['event_date']))) ?></span><strong><?= h(date('d', strtotime($event['event_date']))) ?></strong><small><?= h(date('Y', strtotime($event['event_date']))) ?></small></div>
          </div>
          <div class="event-content">
            <span class="tag"><?= h(date('g:i A', strtotime($event['event_time']))) ?></span>
            <h3><?= h($event['title']) ?></h3>
            <p class="event-description"><?= h($event['description']) ?></p>
            <div class="event-details"><p class="meta"><span>⌖</span> <?= h($event['venue']) ?></p><p class="meta"><span>♧</span> <?= (int)$event['participant_count'] ?> registered</p></div>
            <a class="button card-button" href="register.php?event_id=<?= (int)$event['id'] ?>">Save my spot <span>↗</span></a>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="empty">No events are listed yet. Add events to the database to display them here.</div>
    <?php endif; ?>
    </section>

    <section class="closing-banner">
      <div class="closing-icon">✦</div><div><p class="eyebrow light">YOUR CAMPUS. YOUR COMMUNITY.</p><h2>Show up for the moments<br>you'll talk about later.</h2></div><a href="participants.php" class="button button-light">Explore the community <span>→</span></a>
    </section>
    <footer><strong>Campus Connect</strong> · Bhawanipur Global Campus <span>PHP &amp; MySQL academic project · BCA 5th Semester</span></footer>
  </section>
</main>
</body>
</html>
