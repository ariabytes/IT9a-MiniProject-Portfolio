<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "portfolio_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// FETCH ADMIN
$admin = $conn->query("SELECT * FROM admin LIMIT 1")->fetch_assoc();

// FETCH SKILLS grouped by category
$skills_result = $conn->query("SELECT * FROM skills ORDER BY category, skill_name");
$skills_by_cat = [];
while ($s = $skills_result->fetch_assoc()) {
    $skills_by_cat[$s['category']][] = $s;
}

// FETCH PROJECTS
$projects_result = $conn->query("SELECT * FROM projects ORDER BY date_created DESC");

// FETCH SCHOOL HISTORY
$schools_result = $conn->query("SELECT * FROM school_history ORDER BY year_start DESC");

// FETCH CERTIFICATIONS
$certs_result = $conn->query("SELECT * FROM certifications ORDER BY issued_date DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?> | Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Crimson+Pro:ital,wght@0,400;0,600;1,400;1,600&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>

<body class="portfolio-body">

    <!-- NAVBAR -->
    <nav class="portfolio-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#hero" class="portfolio-nav-brand">
                <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
            </a>
            <div class="d-none d-md-flex gap-1">
                <a href="#skills" class="portfolio-nav-link">Skills</a>
                <a href="#projects" class="portfolio-nav-link">Projects</a>
                <a href="#education" class="portfolio-nav-link">Education</a>
                <a href="#certifications" class="portfolio-nav-link">Certs</a>
                <a href="#contact" class="portfolio-nav-link">Contact</a>
                <a href="login.php" class="portfolio-nav-link" title="Admin">✦</a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 fade-up">
                    <p class="hero-title">— Portfolio</p>
                    <h1 class="hero-name">
                        <?php echo htmlspecialchars($admin['first_name']); ?><br>
                        <em><?php echo htmlspecialchars($admin['last_name']); ?></em>
                    </h1>
                    <p class="hero-bio">
                        <?php echo nl2br(htmlspecialchars($admin['bio'])); ?>
                    </p>
                    <div>
                        <?php if (!empty($admin['email'])): ?>
                            <a href="mailto:<?php echo htmlspecialchars($admin['email']); ?>" class="social-link">✉ Email</a>
                        <?php endif; ?>
                        <?php if (!empty($admin['github_url'])): ?>
                            <a href="<?php echo htmlspecialchars($admin['github_url']); ?>" target="_blank" class="social-link">⌥ GitHub</a>
                        <?php endif; ?>
                        <?php if (!empty($admin['linkedin_url'])): ?>
                            <a href="<?php echo htmlspecialchars($admin['linkedin_url']); ?>" target="_blank" class="social-link">◈ LinkedIn</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-5 text-center fade-up">
                    <div class="pixel-frame d-inline-block">
                        <?php if (!empty($admin['profile_pic'])): ?>
                            <img src="data:image/png;base64,<?php echo base64_encode($admin['profile_pic']); ?>" alt="Profile">
                        <?php else: ?>
                            <img src="default_img.png" alt="Profile">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <hr class="section-rule">
    <p class="vintage-divider py-2">✦ ✦ ✦</p>
    <hr class="section-rule">

    <!-- SKILLS -->
    <section id="skills">
        <div class="container">
            <p class="section-subtitle fade-up">Technical Proficiency</p>
            <h2 class="section-title fade-up">Skills & Tech Stack</h2>
            <hr class="section-rule my-4">

            <?php if (!empty($skills_by_cat)): ?>
                <div class="row g-4 mt-2">
                    <?php foreach ($skills_by_cat as $category => $skills): ?>
                        <div class="col-md-6 col-lg-4 fade-up">
                            <div class="glass-card p-4 h-100">
                                <p class="category-label"><?php echo htmlspecialchars($category ?: 'General'); ?></p>
                                <div>
                                    <?php foreach ($skills as $skill): ?>
                                        <span class="skill-pill">
                                            <?php if (!empty($skill['photo'])): ?>
                                                <img src="data:image/png;base64,<?php echo base64_encode($skill['photo']); ?>" alt="">
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($skill['skill_name']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color:#9aaa8a;">No skills added yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <hr class="section-rule">
    <p class="vintage-divider py-2">✦ ✦ ✦</p>
    <hr class="section-rule">

    <!-- PROJECTS -->
    <section id="projects">
        <div class="container">
            <p class="section-subtitle fade-up">Featured Work</p>
            <h2 class="section-title fade-up">Projects</h2>
            <hr class="section-rule my-4">

            <div class="row g-4 mt-2">
                <?php while ($project = $projects_result->fetch_assoc()): ?>
                    <div class="col-md-6 col-lg-4 fade-up">
                        <div class="glass-card project-card">
                            <?php if (!empty($project['photo'])): ?>
                                <img src="projects_img/<?php echo htmlspecialchars($project['photo']); ?>" class="project-img" alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <?php else: ?>
                                <div class="project-img-placeholder">[ no preview ]</div>
                            <?php endif; ?>
                            <div class="p-4">
                                <?php if (!empty($project['date_created'])): ?>
                                    <p class="project-date mb-1"><?php echo date('F Y', strtotime($project['date_created'])); ?></p>
                                <?php endif; ?>
                                <p class="project-title"><?php echo htmlspecialchars($project['title']); ?></p>

                                <?php if (!empty($project['skill_ids'])): ?>
                                    <div class="mb-2">
                                        <?php
                                        $ids = explode(',', $project['skill_ids']);
                                        $ids_str = implode(',', array_map('intval', $ids));
                                        $snames = $conn->query("SELECT skill_name FROM skills WHERE id IN ($ids_str)");
                                        while ($sn = $snames->fetch_assoc()):
                                        ?>
                                            <span class="skill-pill" style="font-size:0.7rem; padding:3px 8px;"><?php echo htmlspecialchars($sn['skill_name']); ?></span>
                                        <?php endwhile; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($project['description'])): ?>
                                    <p class="project-desc"><?php echo htmlspecialchars($project['description']); ?></p>
                                <?php endif; ?>

                                <div class="pt-2">
                                    <?php if (!empty($project['link'])): ?>
                                        <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank" class="project-link">View →</a>
                                    <?php endif; ?>
                                    <?php if (!empty($project['link2'])): ?>
                                        <a href="<?php echo htmlspecialchars($project['link2']); ?>" target="_blank" class="project-link">Prototype →</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <hr class="section-rule">
    <p class="vintage-divider py-2">✦ ✦ ✦</p>
    <hr class="section-rule">

    <!-- EDUCATION -->
    <section id="education">
        <div class="container">
            <p class="section-subtitle fade-up">Academic Background</p>
            <h2 class="section-title fade-up">Education</h2>
            <hr class="section-rule my-4">

            <div class="row justify-content-center mt-2">
                <div class="col-lg-8">
                    <?php while ($school = $schools_result->fetch_assoc()): ?>
                        <div class="edu-card glass-card p-4 mb-4 fade-up">
                            <p class="edu-years">
                                <?php echo $school['year_start'] ?? '—'; ?>
                                —
                                <?php echo !empty($school['year_end']) ? $school['year_end'] : 'Present'; ?>
                            </p>
                            <div class="d-flex align-items-start gap-3">
                                <?php if (!empty($school['image'])): ?>
                                    <img src="school_img/<?php echo htmlspecialchars($school['image']); ?>" width="48" height="48" style="object-fit:cover; border-radius:6px; border:1px solid rgba(188,169,120,0.2); flex-shrink:0; background: #f8f9fa;">
                                <?php endif; ?>
                                <div>
                                    <p class="edu-school"><?php echo htmlspecialchars($school['school_name']); ?></p>
                                    <?php if (!empty($school['title'])): ?>
                                        <p class="edu-degree"><?php echo htmlspecialchars($school['title']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($school['achievements'])): ?>
                                        <div>
                                            <?php
                                            $achievements = explode(',', $school['achievements']);
                                            foreach ($achievements as $ach):
                                                $ach = trim($ach);
                                                if ($ach):
                                            ?>
                                                    <span class="edu-achievement"><?php echo htmlspecialchars($ach); ?></span>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </section>

    <hr class="section-rule">
    <p class="vintage-divider py-2">✦ ✦ ✦</p>
    <hr class="section-rule">

    <!-- CERTIFICATIONS -->
    <section id="certifications">
        <div class="container">
            <p class="section-subtitle fade-up">Credentials</p>
            <h2 class="section-title fade-up">Certifications</h2>
            <hr class="section-rule my-4">

            <div class="row g-4 mt-2">
                <?php while ($cert = $certs_result->fetch_assoc()): ?>
                    <div class="col-md-6 fade-up">
                        <div class="glass-card p-4 h-100">
                            <div class="d-flex gap-3">
                                <?php if (!empty($cert['image'])): ?>
                                    <img src="certificate_img/<?php echo htmlspecialchars($cert['image']); ?>" class="cert-img" alt="">
                                <?php else: ?>
                                    <img src="default_img.png" class="cert-img" alt="">
                                <?php endif; ?>
                                <div>
                                    <p class="cert-title"><?php echo htmlspecialchars($cert['title']); ?></p>
                                    <?php if (!empty($cert['issuing_org'])): ?>
                                        <p class="cert-org"><?php echo htmlspecialchars($cert['issuing_org']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($cert['issued_date'])): ?>
                                        <p class="cert-date"><?php echo date('F j, Y', strtotime($cert['issued_date'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($cert['description'])): ?>
                                <p class="cert-desc"><?php echo htmlspecialchars($cert['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <hr class="section-rule">
    <p class="vintage-divider py-2">✦ ✦ ✦</p>
    <hr class="section-rule">

    <!-- CONTACT -->
    <section id="contact">
        <div class="container text-center">
            <p class="section-subtitle fade-up">Get In Touch</p>
            <h2 class="contact-heading fade-up">Let's Connect</h2>
            <hr class="section-rule my-4" style="max-width:200px; margin-left:auto; margin-right:auto;">
            <div class="mt-4 fade-up">
                <?php if (!empty($admin['email'])): ?>
                    <a href="mailto:<?php echo htmlspecialchars($admin['email']); ?>" class="social-link">✉ <?php echo htmlspecialchars($admin['email']); ?></a>
                <?php endif; ?>
                <?php if (!empty($admin['github_url'])): ?>
                    <a href="<?php echo htmlspecialchars($admin['github_url']); ?>" target="_blank" class="social-link">⌥ GitHub</a>
                <?php endif; ?>
                <?php if (!empty($admin['linkedin_url'])): ?>
                    <a href="<?php echo htmlspecialchars($admin['linkedin_url']); ?>" target="_blank" class="social-link">◈ LinkedIn</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="portfolio-footer">
        <p class="m-0">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?> — IT9 Mini Project</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/script.js"></script>
</body>

</html>
<?php $conn->close(); ?>