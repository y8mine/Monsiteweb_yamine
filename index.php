<?php
require_once 'config.php';

$user = fetchOne(executeQuery("SELECT * FROM utilisateur WHERE id_utilisateur = 1"));
$experiences = fetchAll(executeQuery("SELECT * FROM experience WHERE id_utilisateur = 1 ORDER BY date_debut DESC"));
$formations = fetchAll(executeQuery("SELECT * FROM formation WHERE id_utilisateur = 1 ORDER BY date_debut DESC"));

$query_projets = "SELECT p.*, 
                  (SELECT GROUP_CONCAT(c.nom_competence SEPARATOR ',') 
                   FROM projet_competence pc 
                   JOIN competence c ON pc.id_competence = c.id_competence 
                   WHERE pc.id_projet = p.id_projet) as skills_list
                  FROM projet p 
                  WHERE p.id_utilisateur = 1 
                  ORDER BY p.date_debut DESC";
$projets = fetchAll(executeQuery($query_projets));

$all_skills = fetchAll(executeQuery("SELECT * FROM competence ORDER BY categorie, niveau DESC"));
$skills_by_cat = [];
foreach ($all_skills as $skill) {
    $cat = $skill['categorie'] ?: 'Autre';
    $skills_by_cat[$cat][] = $skill;
}

$cat_descriptions = [
    'Data Science' => "Analyse statistique, modélisation prédictive et algorithmes de Machine Learning.",
    'Data Engineering' => "Collecte, nettoyage, structuration et gestion des bases de données SQL/NoSQL.",
    'Visualisation' => "Création de tableaux de bord interactifs pour l'aide à la décision.",
    'Programmation' => "Langages de scripts et développement d'applications métiers.",
    'Web' => "Développement Front-End et Back-End pour la restitution de données.",
    'Juridique' => "Conformité RGPD et éthique des données."
];

function getSkillLogo($skillName) {
    $s = strtolower($skillName);
    if (strpos($s, 'python') !== false) return '<i class="devicon-python-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'r ') !== false || $s == 'r') return '<i class="devicon-r-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'sas') !== false) return '<i class="devicon-sas-original colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'tensorflow') !== false || strpos($s, 'keras') !== false) return '<i class="devicon-tensorflow-original colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'sql') !== false) return '<i class="devicon-mysql-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'spark') !== false || strpos($s, 'hadoop') !== false) return '<i class="devicon-apache-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'tableau') !== false) return '<i class="fas fa-chart-bar" style="color:#E97627" title="'.$skillName.'"></i>';
    if (strpos($s, 'power bi') !== false) return '<i class="fas fa-chart-pie" style="color:#F2C811" title="'.$skillName.'"></i>';
    if (strpos($s, 'html') !== false) return '<i class="devicon-html5-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'css') !== false) return '<i class="devicon-css3-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'php') !== false) return '<i class="devicon-php-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'git') !== false) return '<i class="devicon-git-plain colored" title="'.$skillName.'"></i>';
    if (strpos($s, 'géomatique') !== false) return '<i class="fas fa-map-marked-alt" style="color:#00BCD4" title="'.$skillName.'"></i>';
    return '<i class="fas fa-code" title="'.$skillName.'"></i>'; 
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?> | Portfolio</title>
    <link rel="stylesheet" href="public_style.css?v=109"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" type='text/css' href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <canvas id="network-canvas"></canvas>

    <nav class="public-nav">
        <div class="nav-content">
            <div class="logo">
                <span>&lt;</span><?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?><span>/&gt;</span>
            </div>
            <ul class="nav-links">
                <li><a href="#about">01. Profil</a></li>
                <li><a href="#parcours">02. Parcours</a></li>
                <li><a href="#projets">03. Projets</a></li>
                <li><a href="#skills">04. Compétences</a></li>
            </ul>
            <div class="nav-contact-info">
                <a href="tel:<?php echo htmlspecialchars($user['telephone']); ?>" class="contact-item"><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($user['telephone']); ?></a>
                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="contact-item"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></a>
            </div>
        </div>
    </nav>

    <header class="hero-section" id="about">
        <div class="hero-text">
            <span class="badge-role"><i class="fas fa-terminal"></i> Data Scientist Junior</span>
            <h1><?php echo htmlspecialchars($user['prenom']); ?> <span class="highlight"><?php echo htmlspecialchars($user['nom']); ?></span></h1>
            <p class="hero-bio"><?php echo nl2br(htmlspecialchars($user['description'])); ?></p>
            
            <div class="hero-buttons">
                <a href="#projets" class="btn btn-primary"><i class="fas fa-project-diagram"></i> Voir Projets</a>
                
                <?php if (!empty($user['cv_pdf'])): ?>
                    <a href="<?php echo htmlspecialchars($user['cv_pdf']); ?>" target="_blank" class="btn btn-outline"><i class="fas fa-download"></i> Mon CV</a>
                <?php endif; ?>

                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="btn btn-outline"><i class="fas fa-paper-plane"></i> Contact</a>
            </div>
        </div>
        <div class="hero-visual">
            <?php if (!empty($user['avatar'])): ?>
                <div class="avatar-wrapper">
                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="hero-avatar">
                </div>
            <?php else: ?>
                <div class="avatar-placeholder"><i class="fas fa-user-astronaut"></i></div>
            <?php endif; ?>
        </div>
    </header>

    <section id="parcours" class="section-glass">
        <div class="container">
            <h2 class="section-title">Mon Parcours</h2>
            <div class="timeline-wrapper">
                
                <div class="timeline-column">
                    <h3 class="col-title"><i class="fas fa-briefcase"></i> Expériences & Stages</h3>
                    <div class="timeline-track">
                        <?php foreach ($experiences as $exp): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content glass-card">
                                    <div class="t-header">
                                        <?php if($exp['logo']): ?>
                                            <img src="<?php echo htmlspecialchars($exp['logo']); ?>" class="t-logo" alt="Logo">
                                        <?php else: ?>
                                            <div class="t-logo-placeholder"><i class="fas fa-briefcase"></i></div>
                                        <?php endif; ?>
                                        <div>
                                            <h4 class="poste-title"><?php echo htmlspecialchars($exp['titre']); ?></h4>
                                            <span class="t-company"><?php echo htmlspecialchars($exp['entreprise']); ?></span>
                                        </div>
                                    </div>
                                    <span class="t-date">
                                        <?php echo date('M Y', strtotime($exp['date_debut'])); ?> - 
                                        <?php echo $exp['date_fin'] ? date('M Y', strtotime($exp['date_fin'])) : 'Auj.'; ?>
                                    </span>
                                    
                                    <div class="timeline-desc">
                                        <ul>
                                            <?php 
                                            $lines = preg_split('/(\r\n|\n|\r|;)/', $exp['description']);
                                            foreach($lines as $line) {
                                                if(trim($line)) echo '<li>' . htmlspecialchars(trim($line)) . '</li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="timeline-column">
                    <h3 class="col-title"><i class="fas fa-graduation-cap"></i> Formations</h3>
                    <div class="timeline-track">
                        <?php foreach ($formations as $form): ?>
                            <div class="timeline-item">
                                <div class="timeline-dot dot-blue"></div>
                                <div class="timeline-content glass-card">
                                    <div class="t-header">
                                        <?php if(!empty($form['logo'])): ?>
                                            <img src="<?php echo htmlspecialchars($form['logo']); ?>" class="t-logo" alt="Logo École">
                                        <?php else: ?>
                                            <div class="t-logo-placeholder"><i class="fas fa-university"></i></div>
                                        <?php endif; ?>
                                        <div>
                                            <h4><?php echo htmlspecialchars($form['diplome']); ?></h4>
                                            <span class="t-company"><?php echo htmlspecialchars($form['ecole']); ?></span>
                                        </div>
                                    </div>
                                    
                                    <span class="t-date">
                                        <?php echo date('Y', strtotime($form['date_debut'])); ?>
                                        <?php if($form['date_fin']) echo ' - ' . date('Y', strtotime($form['date_fin'])); ?>
                                    </span>

                                    <div class="timeline-desc">
                                        <ul>
                                            <?php 
                
                                            if (stripos($form['diplome'], 'stid') !== false) {
                                                echo '<li><strong>Parcours EMS :</strong> Exploration et Modélisation Statistique.</li>';
                                                echo '<li><strong>Data Science :</strong> Machine Learning (Python, R), Séries Temporelles, Classification.</li>';
                                                echo '<li><strong>Bases de Données :</strong> Conception (MCD/MLD), SQL Avancé, NoSQL.</li>';
                                                echo '<li><strong>Dataviz :</strong> Tableaux de bord interactifs (Tableau, PowerBI, Shiny).</li>';
                                                echo '<li><strong>Projets :</strong> Réalisation de SAÉs (Situation d\'Apprentissage et d\'Évaluation) en équipe.</li>';
                                            } else {
                                                $lines = preg_split('/(\r\n|\n|\r|;)/', $form['description']);
                                                foreach($lines as $line) {
                                                    if(trim($line)) echo '<li>' . htmlspecialchars(trim($line)) . '</li>';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="projets" class="section-glass">
        <div class="container">
            <h2 class="section-title">Projets & Réalisations</h2>
            
            <div class="filters-container">
                <button class="filter-btn active" data-filter="all">Tout</button>
                <button class="filter-btn" data-filter="Data Science">Data Science</button>
                <button class="filter-btn" data-filter="Dataviz">Dataviz</button>
                <button class="filter-btn" data-filter="Data Engineering">Data Engineering</button>
            </div>

            <div class="projects-grid-public">
                <?php foreach ($projets as $proj): ?>
                    <article class="project-card-interactive glass-card" 
                             data-category="<?php echo htmlspecialchars($proj['categorie_projet'] ?? 'Autre'); ?>"
                             onclick="toggleProjectCard(this)">
                        
                        <div class="project-bg-image">
                            <?php if (!empty($proj['image'])): ?>
                                <img src="<?php echo htmlspecialchars($proj['image']); ?>" alt="Cover">
                            <?php else: ?>
                                <div class="no-img-placeholder"><span>Data Project</span></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="project-info-bar">
                            <div>
                                <h3><?php echo htmlspecialchars($proj['titre']); ?></h3>
                                <span class="proj-context"><?php echo htmlspecialchars($proj['contexte'] ?? 'Projet Académique'); ?></span>
                            </div>
                            <span class="proj-year"><?php echo date('Y', strtotime($proj['date_debut'])); ?></span>
                        </div>

                        <div class="project-description-overlay">
                            <div class="overlay-content">
                                <div class="click-to-close"><i class="fas fa-times"></i></div>
                                
                                <h4><?php echo htmlspecialchars($proj['titre']); ?></h4>
                                <div class="overlay-meta">
                                    <span class="meta-tag"><i class="fas fa-university"></i> <?php echo htmlspecialchars($proj['contexte'] ?? 'Projet'); ?></span>
                                    <span class="meta-tag"><i class="far fa-calendar-alt"></i> <?php echo date('M Y', strtotime($proj['date_debut'])); ?></span>
                                </div>

                                <p class="project-desc-text">
                                    <?php echo nl2br(htmlspecialchars($proj['description'])); ?>
                                </p>
                                
                                <div class="tech-stack-logos">
                                    <?php 
                                    if ($proj['skills_list']) {
                                        $techs = explode(',', $proj['skills_list']);
                                        foreach($techs as $tech) echo getSkillLogo(trim($tech));
                                    }
                                    ?>
                                </div>

                                <div class="project-links-overlay">
                                    <?php if($proj['document']): ?>
                                        <a href="<?php echo htmlspecialchars($proj['document']); ?>" target="_blank" class="btn-overlay"><i class="fas fa-file-pdf"></i> Lire le Rapport</a>
                                    <?php endif; ?>
                                    <?php if($proj['lien_code']): ?>
                                        <a href="<?php echo htmlspecialchars($proj['lien_code']); ?>" target="_blank" class="btn-icon-overlay" title="Voir le Code"><i class="fab fa-github"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="skills" class="section-glass">
        <div class="container">
            <h2 class="section-title">Compétences Techniques</h2>
            <div class="skills-wrapper">
                <?php foreach ($skills_by_cat as $categorie => $skills): ?>
                    <div class="skill-category-card glass-card">
                        <h3><?php echo htmlspecialchars($categorie); ?></h3>
                        <p class="skill-cat-desc">
                            <?php echo $cat_descriptions[$categorie] ?? 'Compétences acquises durant le cursus BUT STID.'; ?>
                        </p>
                        <div class="logos-container">
                            <?php foreach ($skills as $skill): ?>
                                <div class="skill-logo-item">
                                    <?php echo getSkillLogo($skill['nom_competence']); ?>
                                    <span class="tooltip"><?php echo htmlspecialchars($skill['nom_competence']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="container">
            <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($user['prenom']); ?>. Portfolio BUT STID.</p>
            <a href="admin.php" class="admin-discrete"><i class="fas fa-lock"></i></a>
        </div>
    </footer>

    <script>
        const filterBtns = document.querySelectorAll('.filter-btn');
        const projects = document.querySelectorAll('.project-card-interactive');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filterValue = btn.getAttribute('data-filter');
                projects.forEach(proj => {
                    const category = proj.getAttribute('data-category');
                    if (filterValue === 'all' || category === filterValue) {
                        proj.style.display = 'block';
                    } else {
                        proj.style.display = 'none';
                    }
                });
            });
        });

        function toggleProjectCard(element) {
            const isClosing = element.classList.contains('active');
            document.querySelectorAll('.project-card-interactive').forEach(p => p.classList.remove('active'));
            if (!isClosing) {
                element.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
            const closeBtn = element.querySelector('.click-to-close');
            if(closeBtn) {
                closeBtn.onclick = (e) => {
                    e.stopPropagation();
                    element.classList.remove('active');
                    document.body.style.overflow = 'auto';
                };
            }
        }
        const canvas = document.getElementById('network-canvas');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];
        const config = { particleCount: 60, connectionDistance: 150, mouseDistance: 200, baseColor: 'rgba(59, 130, 246, 0.4)', lineColor: 'rgba(59, 130, 246, 0.15)' };
        const mouse = { x: null, y: null };
        window.addEventListener('mousemove', (e) => { mouse.x = e.x; mouse.y = e.y; });
        function resize() { width = canvas.width = window.innerWidth; height = canvas.height = window.innerHeight; }
        window.addEventListener('resize', resize);
        resize();
        class Particle {
            constructor() { this.x = Math.random() * width; this.y = Math.random() * height; this.vx = (Math.random() - 0.5) * 0.5; this.vy = (Math.random() - 0.5) * 0.5; this.size = Math.random() * 2 + 1; }
            update() { this.x += this.vx; this.y += this.vy; if (this.x < 0 || this.x > width) this.vx *= -1; if (this.y < 0 || this.y > height) this.vy *= -1; }
            draw() { ctx.fillStyle = config.baseColor; ctx.beginPath(); ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2); ctx.fill(); }
        }
        function init() { particles = []; for (let i = 0; i < config.particleCount; i++) particles.push(new Particle()); }
        function animate() {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].update(); particles[i].draw();
                for (let j = i; j < particles.length; j++) {
                    let dx = particles[i].x - particles[j].x; let dy = particles[i].y - particles[j].y;
                    if (Math.sqrt(dx*dx + dy*dy) < config.connectionDistance) {
                        ctx.beginPath(); ctx.strokeStyle = config.lineColor; ctx.moveTo(particles[i].x, particles[i].y); ctx.lineTo(particles[j].x, particles[j].y); ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }
        init(); animate();
    </script>
</body>
</html>