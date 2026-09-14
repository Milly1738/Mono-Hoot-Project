<?php
// 1. Enable sessions at the absolute top of the file to track login state
session_start();

// Include helper function early so it can be used safely
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$brand = [
    'name'   => 'Mono Hoot Studios',
    'since'  => '2019',
];

$social = ['Instagram', 'LinkedIn', 'Facebook'];

$owlMark = '<svg viewBox="0 0 40 40" fill="none"><circle cx="20" cy="22" r="15" fill="#18150f"/><circle cx="15" cy="20" r="5.5" fill="#fff"/><circle cx="25" cy="20" r="5.5" fill="#fff"/><circle cx="15" cy="20" r="2.3" fill="#18150f"/><circle cx="25" cy="20" r="2.3" fill="#18150f"/><path d="M20 25L17 30H23L20 25Z" fill="#fff"/></svg>';
$owlSmall = '<svg viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="15" fill="#18150f"/><circle cx="15" cy="18" r="5" fill="#fff"/><circle cx="25" cy="18" r="5" fill="#fff"/></svg>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Story — <?= e($brand['name']) ?></title>
<link rel="preconnect" href="https://googleapis.com">
<link rel="preconnect" href="https://gstatic.com" crossorigin>
<link href="https://googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#ffffff;
    --bg-soft:#fbfaf7;
    --ink:#18150f;
    --ink-soft:#6f6a5d;
    --ink-faint:#a7a196;
    --accent:#c6a06a;
    --accent-deep:#a6824f;
    --line:#e9e5db;
    --line-soft:#f1eee6;
  }
  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    margin:0;
    background:var(--bg);
    color:var(--ink);
    font-family:'Inter', sans-serif;
    -webkit-font-smoothing:antialiased;
  }
  a{color:inherit;text-decoration:none;}
  .wrap{max-width:1180px;margin:0 auto;padding:0 40px;}
  h1,h2,h3{
    font-family:'Fraunces', serif;
    font-weight:500;
    margin:0;
    letter-spacing:-0.01em;
  }
  .eyebrow{
    display:flex;align-items:center;gap:8px;
    font-size:12.5px;
    letter-spacing:0.08em;
    color:var(--ink-soft);
    text-transform:uppercase;
    margin-bottom:18px;
  }
  .eyebrow .dot{width:6px;height:6px;border-radius:50%;background:var(--accent);flex:none;}
  .btn{
    display:inline-flex;align-items:center;justify-content:center;
    padding:13px 26px;
    border-radius:999px;
    font-size:14.5px;
    font-weight:500;
    border:1px solid var(--ink);
    cursor:pointer;
    transition:transform .18s ease, background .18s ease, color .18s ease, border-color .18s ease;
    white-space:nowrap;
  }
  .btn-dark{background:var(--ink);color:#fff;border-color:var(--ink);}
  .btn-dark:hover{background:#000;transform:translateY(-1px);}
  .btn-ghost{background:transparent;color:var(--ink);border-color:var(--line);}
  .btn-ghost:hover{border-color:var(--ink);transform:translateY(-1px);}

  /* NAV (Perfect structural match to index) */
  header{
    position:sticky;top:0;z-index:50;
    background:rgba(255,255,255,0.86);
    backdrop-filter:blur(10px);
    border-bottom:1px solid var(--line-soft);
  }
  nav{
    display:flex;align-items:center;justify-content:space-between;
    padding:20px 40px;
    max-width:1180px;margin:0 auto;
  }
  .logo{display:flex;align-items:center;gap:9px;font-family:'Fraunces',serif;font-weight:600;font-size:17px;}
  .logo svg{width:26px;height:26px;}
  .nav-links{display:flex;gap:36px;font-size:14.5px;color:var(--ink-soft);align-items:center;}
  .nav-links a:hover{color:var(--ink);}

  /* STORY CONTENT GRID SETUP */
  .story-hero { padding: 88px 0 60px; }
  .story-layout {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 80px;
    padding-bottom: 120px;
  }
  .story-body h1 {
    font-size: clamp(42px, 5vw, 64px);
    line-height: 1.06;
    margin-bottom: 36px;
  }
  .story-body h1 .accent { color: var(--accent); font-style: italic; }
  
  .story-p {
    font-size: 16px;
    line-height: 1.75;
    color: var(--ink);
    margin: 0 0 24px 0;
  }
  .story-p.lead {
    font-size: 20px;
    line-height: 1.6;
    color: var(--ink);
    font-weight: 400;
    margin-bottom: 36px;
  }

  /* SIDEBAR TIMELINE */
  .story-sidebar {
    position: sticky;
    top: 120px;
    align-self: start;
  }
  .timeline-card {
    background: var(--bg-soft);
    border: 1px solid var(--line-soft);
    border-left: 4px solid var(--accent);
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(24,21,15,0.02);
  }
  .timeline-card h3 {
    font-family: 'Fraunces', serif;
    font-size: 22px;
    font-weight: 500;
    margin-bottom: 12px;
  }
  .timeline-card p {
    margin: 0;
    font-size: 14.5px;
    color: var(--ink-soft);
    line-height: 1.6;
  }

  /* EXPANDED FOOTER SECTION STYLES */
  footer {
    background: #18150f;
    color: #f1eee6;
    padding: 80px 0 40px;
    border-top: 1px solid var(--line);
    font-size: 14px;
  }
  .foot-grid {
    display: grid;
    grid-template-columns: 1.5fr repeat(3, 1fr);
    gap: 40px;
    margin-bottom: 60px;
  }
  .foot-col h4 {
    font-family: 'Fraunces', serif;
    font-size: 16px;
    font-weight: 500;
    color: var(--accent);
    margin: 0 0 20px 0;
    letter-spacing: 0.02em;
  }
  .foot-col p {
    color: #a7a196;
    line-height: 1.6;
    margin: 0 0 16px 0;
    max-width: 280px;
  }
  .foot-links {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  .foot-links li { margin-bottom: 12px; }
  .foot-links a { color: #c9c2b1; transition: color 0.18s ease; }
  .foot-links a:hover { color: #ffffff; }
  .foot-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    background: rgba(198, 160, 106, 0.1);
    color: var(--accent);
    padding: 6px 12px;
    border-radius: 99px;
    border: 1px solid rgba(198, 160, 106, 0.2);
  }
  .foot-status .pulse {
    width: 7px;
    height: 7px;
    background: #52c41a;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(82, 196, 26, 0.3);
  }
  .foot-bottom {
    border-top: 1px solid #2d2922;
    padding-top: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #a7a196;
    font-size: 13px;
  }
  .foot-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
    font-family: 'Fraunces', serif;
    font-weight: 600;
  }
  .foot-logo svg { width: 20px; height: 20px; }

  @media (max-width:860px){
    .wrap{padding:0 22px;}
    nav{padding:18px 22px;}
    .nav-links{display:none;}
    .story-hero { padding-top: 56px; }
    .story-layout { grid-template-columns: 1fr; gap: 40px; padding-bottom: 80px; }
    .story-sidebar { position: relative; top: 0; }
    .foot-grid { grid-template-columns: 1fr; gap: 35px; margin-bottom: 40px; }
    .foot-bottom { flex-direction: column; gap: 16px; text-align: center; }
  }
</style>
</head>
<body>

<header>
  <nav>
    <div class="logo">
      <a href="index.php" style="display: flex; align-items: center; gap: 9px; color: var(--ink);">
        <?= $owlMark ?>
        <span>Mono Hoot</span>
      </a>
    </div>
    
    <div class="nav-links">
      <a href="index.php#work">Work</a>
      <a href="index.php#studio">Studio</a>
      <a href="index.php#process">Process</a>
      <a href="hjournal.php" style="color: var(--ink); font-weight: 600;">Journal</a>
    </div>
    
    <div style="display: flex; align-items: center; gap: 15px;">
      <?php if (isset($_SESSION['username'])): ?>
        <span style="font-size: 14.5px; color: var(--ink-soft);">
          Hi, <?= e($_SESSION['username']) ?>!
        </span>
        <a href="book.php" class="btn btn-ghost">Book a Session</a>
        <?php if ($_SESSION['username'] === 'admin'): ?>
          <a href="admin.php" class="btn btn-ghost">Admin Panel</a>
        <?php endif; ?>
        <a href="logout.php" class="btn btn-dark" style="background-color: #dc3545; border-color: #dc3545;">Log Out</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-ghost" style="margin-right: 5px;">Log In</a>
        <a href="register.php" class="btn btn-dark">Create an Account</a>
      <?php endif; ?>
    </div>
  </nav>
</header>

<main class="wrap">
  <div class="story-hero">
    <div class="eyebrow"><span class="dot"></span> Our Genesis</div>
  </div>

  <div class="story-layout">
    <div class="story-body">
      <h1>How Mono Hoot Studios came to <span class="accent">be.</span></h1>
      
      <p class="story-p lead">Mono Hoot Studios was founded in 2019 out of a simple, rebellious observation: the digital world was getting crowded, loud, and incredibly uninspired.</p>
      
      <p class="story-p">What started as a loose collective of two freelance designers sitting in local Manila coffee shops has grown into a structured, full-service creative node. We chose the owl as our mark because it represents observation, wisdom, and an unhurried approach to craftsmanship in a world that rushes everything.</p>
      
      <p class="story-p">We don't believe in assembly-line layouts or copying trendy styles. Every web experience, custom interface script, and brand design directory we open is built precisely from scratch. We combine deep technology platforms with meticulous design details to produce products that carry weight and mean something to the market.</p>
      
      <br>
      <a href="book.php" class="btn btn-dark">Let's create something together ↗</a>
    </div>

    <div class="story-sidebar">
      <div class="timeline-card">
        <h3>Established 2019</h3>
        <p>Our operation originally opened handles running small-scale identity systems and photo layouts for independent local startup businesses. Today, we bridge the gap between heavy technical systems and premium layout aesthetics across continents.</p>
      </div>
    </div>
  </div>
</main>

<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-col">
        <div class="foot-logo" style="font-size: 18px; margin-bottom: 16px;">
          <?= $owlSmall ?> Mono Hoot Studios
        </div>
        <p>We make things people remember. Creating timeless brand identities, custom digital experiences, and sharp cinematic media solutions since 2019.</p>
        <div class="foot-status">
          <span class="pulse"></span> Booking Window Open
        </div>
      </div>

      <div class="foot-col">
        <h4>Navigation</h4>
        <ul class="foot-links">
          <li><a href="index.php#work">Our Selected Work</a></li>
          <li><a href="index.php#studio">What We Do</a></li>
          <li><a href="hjournal.php">Our Story (Journal)</a></li>
          <li><a href="book.php">Book an Appointment</a></li>
        </ul>
      </div>

      <div class="foot-col">
        <h4>Client Services</h4>
        <ul class="foot-links">
          <li><a href="login.php">Client Login Portal</a></li>
          <li><a href="register.php">Create Free Account</a></li>
          <li><a href="dashboard.php">Client Dashboard</a></li>
        </ul>
      </div>

      <div class="foot-col">
        <h4>Connect &amp; Social</h4>
        <ul class="foot-links">
          <?php foreach ($social as $network): ?>
            <li><a href="#" target="_blank" rel="noopener noreferrer"><?= e($network) ?> →</a></li>
          <?php endforeach; ?>
          <li style="margin-top: 15px; color: #a7a196; font-size: 12.5px;">
            Manila, Philippines<br>
            hello@monohoot.studio
          </li>
        </ul>
      </div>
    </div>

    <div class="foot-bottom">
      <div>&copy; <?= date('Y') ?> <?= e($brand['name']) ?>. All rights reserved. Built locally on XAMPP Stack environment.</div>
      <div style="display: flex; gap: 20px;">
        <a href="#" class="foot-links">Privacy Policy</a>
        <a href="#" class="foot-links">Sitemap</a>
      </div>
    </div>
  </div>
</footer>

</body>
</html>
