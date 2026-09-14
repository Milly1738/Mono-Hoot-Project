<?php
/**
 * Mono Hoot Studios — homepage
 * All copy/content lives in PHP arrays below, so the page can be edited
 * without touching markup — swap these for a database or CMS call later.
 */
session_start();
$databaseServices = [];
require_once 'db.php';
$serviceQuery = $conn->query("SELECT * FROM services ORDER BY title ASC");
$databaseServices = $serviceQuery->fetchAll(PDO::FETCH_ASSOC);

$brand = [
    'name'   => 'Mono Hoot Studios',
    'since'  => '2019',
];

$hero = [
    'eyebrow'  => 'Mono Hoot Studios — Est. ' . $brand['since'],
    'headline' => 'We make things people',
    'accent'   => 'remember.',
    'body'     => 'Brand identity, digital products, and motion for companies that want to mean something.',
];

$stats = [
    ['num' => '130+', 'label' => 'Projects shipped'],
    ['num' => '20+',  'label' => 'Concepts produced'],
    ['num' => '4',    'label' => 'Continents reached'],
    ['num' => '100%', 'label' => 'Client retention'],
];

$services = [
    ['tag' => 'Gd', 'title' => 'Graphic Design',        'desc' => 'Posters, logos, layouts, and visual designs made for different projects.'],
    ['tag' => 'Wd', 'title' => 'Web Development',        'desc' => 'Websites and digital interfaces designed to be functional and easy to use.'],
    ['tag' => 'Pv', 'title' => 'Photo & Video Editing',  'desc' => "Refined visuals and motion pieces that carry a brand's story forward."],
    ['tag' => 'Cp', 'title' => 'Creative Projects',      'desc' => 'Personal and school projects that showcase ideas, skills, and creativity.'],
];

$projects = [
    ['art' => 'notebook', 'cat' => 'Brand',          'name' => 'MonoHoot', 'year' => '2025'],
    ['art' => 'gallery',  'cat' => 'Product Design',  'name' => 'Mono',     'year' => '2025'],
    ['art' => 'office',   'cat' => 'Campaign',        'name' => 'Manila',   'year' => '2024'],
    ['art' => 'laptop',   'cat' => 'Motion',          'name' => 'Sleek',    'year' => '2024'],
];

$quote = [
    'text' => "Great design isn't just about how something looks. It's about how it makes people feel.",
    'name' => 'Leila U.',
    'role' => 'CEO, Areneo',
];

$clients = [
    ['name' => 'Leila U.', 'role' => 'CEO, Areneo',              'active' => true],
    ['name' => 'Jamie V.', 'role' => 'Founder, Nordic Studio',   'active' => false],
    ['name' => 'Owen R.',  'role' => 'Founder, Fieldwork',       'active' => false],
];

$cta = [
    'eyebrow' => 'Ready to begin',
    'line1'   => "Let's make something",
    'accent'  => 'worth remembering.',
];

$social = ['Instagram', 'LinkedIn', 'Facebook'];

// Small helper so copy can contain special characters safely.
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// --- CONTACT FORM PROCESSING LOGIC ---
$contactMessage = "";
$contactClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
    require_once 'db.php'; // Pull in database connection parameters

    $fullName = trim($_POST['full_name']);
    $email    = trim($_POST['email']);
    $message  = trim($_POST['message']);

    if (empty($fullName) || empty($email) || empty($message)) {
        $contactMessage = "Please fill in all fields.";
        $contactClass = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactMessage = "Please enter a valid email address.";
        $contactClass = "error";
    } else {
        try {
            // Securely insert the inquiry into your database table
            $stmt = $conn->prepare("INSERT INTO contact_submissions (full_name, email, message) VALUES (:full_name, :email, :message)");
            $stmt->execute([
                'full_name' => $fullName,
                'email'    => $email,
                'message'  => $message
            ]);

            $contactMessage = "Thank you! Your message has been sent successfully.";
            $contactClass = "success";
        } catch (PDOException $e) {
            $contactMessage = "Database error: " . $e->getMessage();
            $contactClass = "error";
        }
    }
}
// -------------------------------------

$owlMark = '<svg viewBox="0 0 40 40" fill="none"><circle cx="20" cy="22" r="15" fill="#18150f"/><circle cx="15" cy="20" r="5.5" fill="#fff"/><circle cx="25" cy="20" r="5.5" fill="#fff"/><circle cx="15" cy="20" r="2.3" fill="#18150f"/><circle cx="25" cy="20" r="2.3" fill="#18150f"/><path d="M20 25L17 30H23L20 25Z" fill="#fff"/></svg>';
$owlSmall = '<svg viewBox="0 0 40 40" fill="none"><circle cx="20" cy="20" r="15" fill="#18150f"/><circle cx="15" cy="18" r="5" fill="#fff"/><circle cx="25" cy="18" r="5" fill="#fff"/></svg>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($brand['name']) ?> — We make things people remember.</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,500&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
  img{max-width:100%;display:block;}
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

  /* NAV */
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
  .nav-links{display:flex;gap:36px;font-size:14.5px;color:var(--ink-soft);}
  .nav-links a:hover{color:var(--ink);}

  /* HERO */
  .hero{padding:88px 0 0;}
  .hero-top{display:flex;justify-content:space-between;align-items:flex-end;gap:60px;}
  .hero h1{
    font-size:clamp(42px,6vw,72px);
    line-height:1.04;
    max-width:640px;
  }
  .hero h1 .accent{color:var(--accent);font-style:italic;}
  .hero-side{display:flex;flex-direction:column;align-items:flex-end;gap:26px;max-width:340px;text-align:right;}
  .hero-side p{color:var(--ink-soft);font-size:15px;line-height:1.6;margin:0;}
  .hero-actions{display:flex;gap:12px;}

  .stats{
    margin-top:74px;
    border-top:1px solid var(--line);
    padding:34px 0;
    display:grid;
    grid-template-columns:repeat(4,1fr);
  }
  .stat{border-left:1px solid var(--line);padding-left:26px;}
  .stat:first-child{border-left:none;padding-left:0;}
  .stat .num{font-family:'Fraunces',serif;font-size:34px;font-weight:500;}
  .stat .label{font-size:13px;color:var(--ink-faint);margin-top:4px;}

  /* SERVICES */
  .services{padding:120px 0;border-top:1px solid var(--line-soft);}
  .services-grid{display:grid;grid-template-columns:0.85fr 1.15fr;gap:70px;align-items:start;}
  .services h2{font-size:clamp(30px,3.4vw,40px);line-height:1.18;max-width:380px;}
  .service-list{border-top:1px solid var(--line);}
  .service-row{
    display:grid;grid-template-columns:40px 1fr auto;gap:20px;align-items:center;
    padding:26px 4px;border-bottom:1px solid var(--line);
  }
  .service-icon{
    width:34px;height:34px;border-radius:9px;background:var(--bg-soft);border:1px solid var(--line);
    display:flex;align-items:center;justify-content:center;color:var(--ink-soft);font-size:13px;font-family:'Fraunces',serif;
  }
  .service-row h3{font-size:16.5px;font-weight:500;margin-bottom:5px;}
  .service-row p{margin:0;font-size:14px;color:var(--ink-soft);line-height:1.5;}
  .service-arrow{color:var(--ink-faint);font-size:18px;transition:transform .18s ease, color .18s ease;}
  .service-row:hover .service-arrow{color:var(--ink);transform:translate(2px,-2px);}

  /* PROJECTS */
  .projects{padding-bottom:120px;}
  .projects-head{display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:38px;}
  .projects-head h2{font-size:clamp(28px,3vw,36px);}
  .all-work{font-size:14px;color:var(--ink-soft);display:flex;align-items:center;gap:6px;}
  .all-work:hover{color:var(--ink);}
  .project-grid{
    display:grid;grid-template-columns:1fr 1fr;gap:22px;
  }
  .project-card{
    position:relative;
    border-radius:14px;
    overflow:hidden;
    aspect-ratio:4/3.1;
    color:#fff;
    isolation:isolate;
  }
  .project-card .art{position:absolute;inset:0;z-index:-2;}
  .project-card::after{
    content:"";position:absolute;inset:0;z-index:-1;
    background:linear-gradient(to top, rgba(10,9,6,.72) 0%, rgba(10,9,6,0) 42%);
  }
  .project-meta{
    position:absolute;left:22px;bottom:20px;right:22px;
    display:flex;justify-content:space-between;align-items:flex-end;
  }
  .project-meta .cat{font-size:11.5px;opacity:.75;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:4px;}
  .project-meta .name{font-family:'Fraunces',serif;font-size:20px;font-weight:500;}
  .project-meta .year{font-size:12.5px;opacity:.75;}

  .art-notebook{background:
    radial-gradient(circle at 30% 25%, #efe3cf 0%, #d9c6a2 45%, #b89a6e 100%);
  }
  .art-notebook .paper{
    position:absolute;left:14%;top:20%;width:52%;height:56%;
    background:#4d4638;border-radius:3px;
    box-shadow:0 20px 40px rgba(0,0,0,.25);
    transform:rotate(-3deg);
  }
  .art-notebook .card{
    position:absolute;left:30%;top:34%;width:34%;height:20%;
    background:#fdfbf6;border-radius:4px;
    box-shadow:0 10px 22px rgba(0,0,0,.2);
    transform:rotate(4deg);
    display:flex;align-items:center;justify-content:center;
  }
  .art-notebook .card svg{width:26px;height:26px;}
  .art-notebook .cup{
    position:absolute;right:16%;bottom:14%;width:22%;height:26%;
    background:#f4f0e8;border-radius:0 0 40% 40%/0 0 55% 55%;
    box-shadow:0 10px 20px rgba(0,0,0,.2);
  }

  .art-gallery{background:linear-gradient(160deg,#26241f,#0e0d0a 70%);}
  .art-gallery .frame{
    position:absolute;left:50%;top:16%;transform:translateX(-50%);
    width:38%;height:64%;background:#050403;border:6px solid #1c1a15;
    box-shadow:0 30px 60px rgba(0,0,0,.55);
    display:flex;align-items:center;justify-content:center;
  }
  .art-gallery .frame .inner{
    width:70%;height:56%;background:#f6f2e9;border-radius:2px;
    display:flex;align-items:center;justify-content:center;
  }
  .art-gallery .frame .inner svg{width:34%;}

  .art-office{background:linear-gradient(180deg,#eae6dc,#cfc9ba);}
  .art-office .wall{position:absolute;inset:0 0 38% 0;background:#efece2;}
  .art-office .floor{position:absolute;left:0;right:0;bottom:0;height:38%;background:linear-gradient(180deg,#b7a988,#8f7f5f);}
  .art-office .mark{
    position:absolute;left:50%;top:32%;transform:translateX(-50%);
    width:64px;height:64px;
  }
  .art-office .plant{
    position:absolute;left:10%;bottom:10%;width:16%;height:44%;
    background:linear-gradient(180deg, transparent 60%, #5c6b48 60%);
    border-radius:6px 6px 0 0;
  }

  .art-laptop{background:linear-gradient(160deg,#f2efe8,#c9c2b1);}
  .art-laptop .screen{
    position:absolute;left:8%;top:12%;width:84%;height:70%;
    background:linear-gradient(155deg,#fbfaf6,#d9d3c4);
    border-radius:10px 30px 10px 10px/10px 30px 10px 10px;
    transform:perspective(700px) rotateY(-8deg) rotateX(2deg);
    box-shadow:0 30px 50px rgba(0,0,0,.28);
    display:flex;align-items:center;justify-content:center;
  }
  .art-laptop .screen svg{width:34%;}
  .art-laptop .bar{position:absolute;top:6%;left:0;right:0;height:6%;display:flex;gap:5px;align-items:center;padding:0 5%;}
  .art-laptop .bar span{width:6px;height:6px;border-radius:50%;background:#c7c0ac;}

  /* TESTIMONIALS */
  .testimonials{padding:110px 0;border-top:1px solid var(--line-soft);}
  .test-grid{display:grid;grid-template-columns:1.5fr 1fr;gap:70px;align-items:start;}
  .quote{
    font-family:'Fraunces',serif;font-weight:500;
    font-size:clamp(26px,3.1vw,38px);
    line-height:1.28;
    max-width:560px;
  }
  .quote-by{margin-top:28px;font-size:14.5px;color:var(--ink-soft);}
  .quote-by strong{color:var(--ink);font-weight:500;}
  .client-list{display:flex;flex-direction:column;gap:10px;}
  .client-card{
    padding:16px 18px;border-radius:12px;border:1px solid var(--line);
    display:flex;flex-direction:column;gap:2px;
  }
  .client-card.active{background:var(--bg-soft);border-color:var(--line);}
  .client-card .name{font-size:14.5px;font-weight:500;}
  .client-card .role{font-size:13px;color:var(--ink-faint);}

  /* CTA */
  .cta{padding:130px 0;text-align:center;border-top:1px solid var(--line-soft);}
  .cta .eyebrow{justify-content:center;}
  .cta h2{
    font-size:clamp(34px,5.4vw,58px);
    line-height:1.12;
  }
  .cta h2 .accent{color:var(--accent);font-style:italic;}
  .cta-actions{display:flex;gap:14px;justify-content:center;margin-top:36px;}

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
  .foot-links li {
    margin-bottom: 12px;
  }
  .foot-links a {
    color: #c9c2b1;
    transition: color 0.18s ease;
  }
  .foot-links a:hover {
    color: #ffffff;
  }
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
  .foot-logo svg {
    width: 20px;
    height: 20px;
  }

  @media (max-width: 860px) {
    .foot-grid {
      grid-template-columns: 1fr;
      gap: 35px;
      margin-bottom: 40px;
    }
    .foot-bottom {
      flex-direction: column;
      gap: 16px;
      text-align: center;
    }
  }

    /* CONTACT FORM SECTION STYLES */
  .contact-section { padding: 100px 0; border-top: 1px solid var(--line-soft); background: var(--bg-soft); }
  .contact-container { max-width: 600px; margin: 0 auto; background: var(--bg); padding: 40px; border-radius: 14px; border: 1px solid var(--line); box-shadow: 0 4px 20px rgba(24,21,15,0.04); }
  .contact-section h2 { font-size: 36px; margin-bottom: 8px; text-align: center; }
  .contact-section .subtext { font-size: 15px; color: var(--ink-soft); text-align: center; margin-bottom: 30px; }
  .form-group { margin-bottom: 20px; text-align: left; }
  .form-group label { display: block; font-size: 13.5px; font-weight: 600; margin-bottom: 6px; color: var(--ink); }
  .form-control { width: 100%; padding: 12px 16px; border: 1px solid var(--line); border-radius: 8px; font-family: inherit; font-size: 14.5px; background: var(--bg); color: var(--ink); transition: border-color 0.18s; }
  .form-control:focus { outline: none; border-color: var(--accent); }
  textarea.form-control { resize: vertical; min-height: 120px; }
  .btn-submit { width: 100%; padding: 14px; background-color: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; transition: background-color 0.18s, transform 0.18s; }
  .btn-submit:hover { background-color: var(--accent-deep); transform: translateY(-1px); }
  .alert { padding: 12px; margin-bottom: 20px; border-radius: 8px; font-weight: 500; text-align: center; }
  .alert.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
  .alert.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
</head>
<body>

<header>
  <nav>
    <!-- Logo Icon and Brand Text Grouped Together -->
    <div class="logo">
      <a href="index.php" style="display: flex; align-items: center; gap: 9px; color: var(--ink);">
        <?= $owlMark ?>
        <span>Mono Hoot</span>
      </a>
    </div>
    <div class="nav-links">
      <a href="#work">Work</a>
      <a href="#studio">Studio</a>
      <a href="process.php">Process</a>
      <a href="hjournal.php">Journal</a>
    </div>
    
<div style="display: flex; align-items: center; gap: 15px;">
  <?php if (isset($_SESSION['username'])): ?>
    <span style="font-size: 14.5px; color: var(--ink-soft);">
      Hi, <strong><?= e($_SESSION['username']) ?></strong>!
    </span>
    <a href="journal.php" class="btn btn-ghost" style="padding: 8px 14px; font-size:13px;">My Journal</a>
    <a href="book.php" class="btn btn-ghost" style="padding: 8px 14px; font-size:13px;">Book a Session</a>
    <?php if ($_SESSION['username'] === 'admin'): ?>
      <a href="admin.php" class="btn btn-ghost" style="padding: 8px 14px; font-size:13px;">Admin Panel</a>
    <?php endif; ?>
    <a href="logout.php" class="btn btn-dark" style="background-color: #dc3545; border-color: #dc3545;">Log Out</a>
  <?php else: ?>
    <!-- Only show standard directional action buttons for public guests -->
    <a href="login.php" class="btn btn-ghost" style="margin-right: 5px;">Log In</a>
    <a href="register.php" class="btn btn-dark">Create an Account</a>
  <?php endif; ?>
</div>



  </nav>
</header>

<main>

  <!-- HERO -->
  <section class="hero wrap">
    <div class="hero-top">
      <div>
        <div class="eyebrow"><span class="dot"></span><?= e($hero['eyebrow']) ?></div>
        <h1><?= e($hero['headline']) ?> <span class="accent"><?= e($hero['accent']) ?></span></h1>
      </div>
      <div class="hero-side">
        <p><?= e($hero['body']) ?></p>
        <div class="hero-actions">
          <a href="#work" class="btn btn-ghost">View our work</a>
          <a href="#start" class="btn btn-dark">Let's talk</a>
        </div>
      </div>
    </div>

    <div class="stats">
      <?php foreach ($stats as $stat): ?>
      <div class="stat">
        <div class="num"><?= e($stat['num']) ?></div>
        <div class="label"><?= e($stat['label']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- SERVICES -->
  <section class="services wrap" id="studio">
    <div class="services-grid">
      <div>
        <div class="eyebrow"><span class="dot"></span>What we do</div>
        <h2>Work that combines design, technology, and creativity.</h2>
      </div>
      <div class="service-list">
        <?php foreach ($databaseServices as $service): ?>
        <div class="service-row" style="grid-template-columns: 40px 1fr auto; align-items: center; padding: 26px 4px; border-bottom: 1px solid var(--line);">
          <div class="service-icon" style="width:34px; height:34px; border-radius:9px; background:var(--bg-soft); border:1px solid var(--line); display:flex; align-items:center; justify-content:center; color:var(--ink-soft); font-size:13px; font-family:'Fraunces',serif;"><?= e($service['tag']) ?></div>
          <div style="padding-left: 10px;">
            <h3 style="font-size:16.5px; font-weight:500; margin:0 0 5px 0;"><?= e($service['title']) ?></h3>
            <p style="margin:0; font-size:14px; color:var(--ink-soft); line-height:1.5;"><?= e($service['desc']) ?></p>
          </div>
          <!-- INJECTED PRICING ACCENT BADGE -->
          <div style="font-family: 'Fraunces', serif; font-size: 16px; font-weight: 600; color: var(--accent); white-space: nowrap; background: rgba(198, 160, 106, 0.08); padding: 6px 14px; border-radius: 6px; border: 1px solid rgba(198, 160, 106, 0.15);">
            <?php
              // If the database column exists, show it; otherwise show a clean default rate
              if (isset($service['price']) && !empty($service['price'])) {
                  echo "₱" . number_format($service['price']);
              } else {
                  // Safe default text while you finish setting up your database column
                  echo "Custom Quote";
              }
            ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- PROJECTS -->
  <section class="projects wrap" id="work">
    <div class="projects-head">
      <div>
        <div class="eyebrow"><span class="dot"></span>Selected work</div>
        <h2>Recent projects</h2>
      </div>
      <a href="#" class="all-work">All work →</a>
    </div>

    <div class="project-grid">
      <?php foreach ($projects as $project): ?>
      <div class="project-card">
        <?php if ($project['art'] === 'notebook'): ?>
          <div class="art art-notebook">
            <div class="paper"></div>
            <div class="card"><?= $owlSmall ?></div>
            <div class="cup"></div>
          </div>
        <?php elseif ($project['art'] === 'gallery'): ?>
          <div class="art art-gallery">
            <div class="frame"><div class="inner"><?= $owlSmall ?></div></div>
          </div>
        <?php elseif ($project['art'] === 'office'): ?>
          <div class="art art-office">
            <div class="wall"></div>
            <div class="floor"></div>
            <div class="mark"><?= $owlSmall ?></div>
            <div class="plant"></div>
          </div>
        <?php elseif ($project['art'] === 'laptop'): ?>
          <div class="art art-laptop">
            <div class="screen">
              <div class="bar"><span></span><span></span><span></span></div>
              <?= $owlSmall ?>
            </div>
          </div>
        <?php endif; ?>
        <div class="project-meta">
          <div>
            <span class="cat"><?= e($project['cat']) ?></span>
            <span class="name"><?= e($project['name']) ?></span>
          </div>
          <span class="year"><?= e($project['year']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="testimonials wrap">
    <div class="test-grid">
      <div>
        <div class="eyebrow"><span class="dot"></span>What clients say</div>
        <p class="quote">"<?= e($quote['text']) ?>"</p>
        <div class="quote-by"><strong><?= e($quote['name']) ?></strong> — <?= e($quote['role']) ?></div>
      </div>
      <div class="client-list">
        <?php foreach ($clients as $client): ?>
        <div class="client-card<?= $client['active'] ? ' active' : '' ?>">
          <div class="name"><?= e($client['name']) ?></div>
          <div class="role"><?= e($client['role']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

    <!-- CONTACT FORM -->
  <section class="contact-section" id="start">
    <div class="wrap">
      <div class="contact-container">
        <div class="eyebrow" style="justify-content: center;"><span class="dot"></span><?= e($cta['eyebrow']) ?></div>
        <h2><?= e($cta['line1']) ?> <span class="accent" style="color: var(--accent); font-family: 'Fraunces', serif; font-style: italic; font-weight: 500;"><?= e($cta['accent']) ?></span></h2>
        <p class="subtext">Drop us a line below and we will get back to you within 24 hours.</p>

        <!-- Display dynamic submission notifications -->
        <?php if (!empty($contactMessage)): ?>
          <div class="alert <?= $contactClass; ?>" style="padding: 12px; margin-bottom: 20px; border-radius: 6px; font-weight: 500; text-align: center; <?= $contactClass === 'error' ? 'background:#f8d7da; color:#721c24;' : 'background:#d4edda; color:#155724;'; ?>">
            <?= e($contactMessage) ?>
          </div>
        <?php endif; ?>

        <form action="index.php#start" method="POST">
          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
          </div>
          
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
          </div>
          
          <div class="form-group">
            <label for="message">Your Message</label>
            <textarea id="message" name="message" class="form-control" placeholder="Tell us about your project or ideas..." required></textarea>
          </div>
          
          <button type="submit" name="submit_contact" class="btn btn-dark" style="width: 100%; padding: 14px;">Send Message ↗</button>
        </form>
      </div>
    </div>
  </section>


</main>

<footer>
  <div class="wrap">
    <!-- Main Footer Directory Links Grid -->
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
          <li><a href="#work">Our Selected Work</a></li>
          <li><a href="#studio">What We Do</a></li>
          <li><a href="book.php">Book an Appointment</a></li>
          <li><a href="#start">Get in Touch</a></li>
        </ul>
      </div>

      <div class="foot-col">
        <h4>Client Services</h4>
        <ul class="foot-links">
          <li><a href="login.php">Client Login Portal</a></li>
          <li><a href="register.php">Create Free Account</a></li>

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

    <!-- Copyright Attribution & Subtext Strip -->
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
