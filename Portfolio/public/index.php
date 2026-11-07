<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
$site = site_data();
$projects = array_slice(projects_data(true), 0, 3);
$albums = array_slice(albums_data(), 0, 3);
$hero = public_asset((string)($site['hero_image'] ?? ''));
$menu = public_asset((string)($site['menu_image'] ?? ''));
$email = safe_url((string)($site['email'] ?? ''), true);
$socials = [
  'Instagram' => safe_url((string)($site['instagram'] ?? '')),
  'GitHub' => safe_url((string)($site['github'] ?? '')),
  'LinkedIn' => safe_url((string)($site['linkedin'] ?? '')),
];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#101012"><meta name="description" content="Student portfolio of <?= h($site['name'] ?? 'Philip Antonio Dineros') ?>">
  <title><?= h($site['name'] ?? 'Philip Antonio Dineros') ?> — Student Portfolio</title>
  <link rel="stylesheet" href="/assets/css/site.css">
  <link rel="stylesheet" href="/assets/css/music.css">
  <script defer src="/assets/js/site.js"></script>
</head>
<body style="--acid: <?= h($site['accent'] ?? '#d8ff00') ?>">
<a class="skip-link" href="#main">Skip to content</a>
<header class="topbar" id="top">
  <a class="brand" href="#top" aria-label="<?= h($site['name'] ?? '') ?> home"><span class="brand-mark">PD<span>®</span></span><span class="brand-name"><?= h($site['name'] ?? 'Philip Antonio Dineros') ?></span></a>
  <nav class="main-nav" aria-label="Main navigation"><a class="active" href="#top">Home</a><a href="#projects">Projects</a><a href="#about">About</a><a href="#contact">Contact</a></nav>
  <a class="top-contact" href="#contact">LET’S TALK <span aria-hidden="true">↗</span></a>
</header>
<button class="scroll-menu" id="scrollMenu" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="menuPanel">
  <img src="<?= h($menu ?: '/assets/images/menu-sticker.svg') ?>" alt="" id="menuImage"><span class="menu-glyph" aria-hidden="true">☰</span>
</button>
<div class="menu-backdrop" id="menuBackdrop" hidden></div>
<nav class="menu-panel" id="menuPanel" aria-label="Quick navigation" hidden>
  <div class="menu-panel-head"><span>JUMP TO</span><button type="button" class="menu-close" id="menuClose" aria-label="Close navigation">×</button></div>
  <a href="#top">01 / HOME</a><a href="#projects">02 / PROJECTS</a><a href="#about">03 / ABOUT</a><a href="#music">04 / MUSIC</a><a href="#contact">05 / CONTACT</a>
</nav>
<main id="main">
  <section class="hero section-wrap" aria-labelledby="heroTitle">
    <div class="hero-copy">
      <div class="eyebrow"><span class="eyebrow-line"></span><?= h($site['eyebrow'] ?? '') ?></div>
      <h1 id="heroTitle"><span><?= h($site['headline_top'] ?? 'I MAKE') ?></span><strong><?= h($site['headline_accent'] ?? 'DIGITAL THINGS') ?><i aria-hidden="true">✳</i></strong></h1>
      <p class="hero-intro"><?= h($site['intro'] ?? '') ?></p>
      <div class="hero-tags"><span>STUDENT</span><span>DESIGNER</span><span>DEVELOPER</span><span>CURIOUS</span></div>
      <div class="hero-actions"><a class="button-acid" href="#projects">VIEW PROJECTS <span>↘</span></a><a class="button-quiet" href="#about">ABOUT ME <span>↗</span></a></div>
      <div class="hero-note">/ <?= h($site['hero_note'] ?? '') ?></div>
    </div>
    <div class="hero-art <?= $hero ? 'has-upload' : '' ?>" aria-label="Abstract acid graphic">
      <?php if ($hero): ?><img class="hero-upload" src="<?= h($hero) ?>" alt="<?= h($site['hero_alt'] ?? 'Abstract portfolio hero artwork') ?>">
      <?php else: ?><div class="hero-orbit" aria-hidden="true"><div class="orb-core"></div><span class="orbit orbit-a"></span><span class="orbit orbit-b"></span><span class="orbit orbit-c"></span></div><?php endif; ?>
      <div class="art-stamp">PHIL<br><b>20</b></div><div class="art-cross cross-a">＋</div><div class="art-cross cross-b">＋</div><div class="art-bars">/// /// ///</div><div class="art-caption">PHILIP<br>DINEROS <span>↗</span></div>
    </div>
    <div class="hero-bottomline"><span>PHILIP ANTONIO DINEROS</span><a href="#projects">SCROLL TO EXPLORE ↓</a></div>
  </section>
  <section class="projects section-wrap" id="projects" aria-labelledby="projectsTitle">
    <div class="section-heading"><div><span class="section-kicker">SELECTED WORK / 01—<?= count($projects) < 10 ? '0' . count($projects) : count($projects) ?></span><h2 id="projectsTitle">PROJECTS<span class="acid-slash"> /</span></h2></div><a class="all-projects" href="projects.php">ALL PROJECTS <span>↗</span></a></div>
    <div class="project-grid">
    <?php foreach ($projects as $index => $project): $projectImage = public_asset((string)($project['image'] ?? '')); ?>
      <article class="project-card">
        <a class="project-visual visual-<?= ($index % 3) + 1 ?>" href="project.php?id=<?= rawurlencode((string)$project['id']) ?>" aria-label="View <?= h($project['title']) ?>">
          <?php if ($projectImage): ?><img src="<?= h($projectImage) ?>" alt="<?= h($project['alt'] ?? '') ?>"><?php else: ?><span class="visual-art" aria-hidden="true"><i></i><b><?= h(str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT)) ?></b><em>FIG.<?= h((string)($index + 1)) ?></em></span><?php endif; ?>
          <span class="card-arrow" aria-hidden="true">↗</span>
        </a>
        <div class="project-meta"><span><?= h($project['category'] ?? 'PROJECT') ?></span><span>0<?= $index + 1 ?></span></div>
        <h3><a href="project.php?id=<?= rawurlencode((string)$project['id']) ?>"><?= h($project['title']) ?></a></h3>
        <p><?= h($project['summary'] ?? '') ?></p>
        <div class="tech-tags"><?php foreach (array_slice($project['tools'] ?? [], 0, 4) as $tool): ?><span><?= h($tool) ?></span><?php endforeach; ?></div>
      </article>
    <?php endforeach; ?>
    <?php if (!$projects): ?><p class="empty-note">New work is on the way. Check back soon.</p><?php endif; ?>
    </div>
  </section>
  <section class="about-music section-wrap" id="about">
    <article class="about-panel panel-line"><span class="section-kicker">A LITTLE ABOUT ME / 02</span><h2>STILL<br>IN THE<br><span>MAKING.</span></h2><p><?= h($site['about'] ?? '') ?></p><div class="skills-list"><?php foreach (array_slice($site['skills'] ?? [], 0, 6) as $skill): ?><span><?= h($skill) ?></span><?php endforeach; ?></div></article>
    <article class="music-panel panel-line" id="music"><span class="section-kicker">OFF THE CLOCK / 03</span><h2>MUSIC <span>///</span><i class="sound-bars" aria-hidden="true"><b></b><b></b><b></b><b></b><b></b><b></b></i></h2><p><?= h($site['music_intro'] ?? '') ?></p><div class="vinyl-wrap" aria-hidden="true"><div class="vinyl"><i></i></div><span>ON<br>REPEAT</span></div><div class="genres"><?php foreach (array_slice($site['music_genres'] ?? [], 0, 6) as $genre): ?><span><?= h($genre) ?></span><?php endforeach; ?></div><div class="album-grid" aria-label="Favorite albums"><?php for ($slot = 0; $slot < 3; $slot++): $album = $albums[$slot] ?? null; $cover = $album ? public_asset((string)($album['image'] ?? '')) : ''; ?><article class="album-card"><?php if ($cover): ?><img src="<?= h($cover) ?>" alt="<?= h($album['alt'] ?? ('Album cover for ' . $album['title'])) ?>"><?php else: ?><div class="album-placeholder" aria-hidden="true"><span>ALBUM<br>COVER</span><b>0<?= $slot + 1 ?></b></div><?php endif; ?><h3><?= $album ? h($album['title']) : 'ALBUM TITLE' ?></h3></article><?php endfor; ?></div><?php if ($musicUrl = safe_url((string)($site['music_url'] ?? ''))): ?><a class="text-link" href="<?= h($musicUrl) ?>" target="_blank" rel="noopener noreferrer">PLAYLIST / LISTEN ↗</a><?php endif; ?></article>
    <article class="contact-panel" id="contact"><div class="contact-burst"><span>LET’S<br>MAKE<br>NOISE.</span><i aria-hidden="true">✳</i></div><span class="section-kicker">HAVE A GOOD ONE IN MIND?</span><h2>LET’S TALK.</h2><?php if ($email): ?><a class="button-purple" href="<?= h($email) ?>">CONTACT PHILIP <span>↗</span></a><?php else: ?><p class="contact-hint">Add your email in the admin panel to make this button live.</p><?php endif; ?><div class="social-links"><?php foreach ($socials as $label => $url): if ($url): ?><a href="<?= h($url) ?>" target="_blank" rel="noopener noreferrer"><?= h(strtoupper($label)) ?> ↗</a><?php endif; endforeach; ?></div><?php if ($resume = safe_url((string)($site['resume_url'] ?? ''))): ?><a class="resume-link" href="<?= h($resume) ?>" target="_blank" rel="noopener noreferrer">RÉSUMÉ PDF ↗</a><?php endif; ?></article>
  </section>
</main>
<footer class="site-footer section-wrap"><span>© <?= date('Y') ?> PHILIP ANTONIO DINEROS</span><span>MADE WITH CURIOSITY <b>✳</b></span><a href="#top">BACK TO TOP ↑</a></footer>
</body></html>
