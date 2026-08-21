<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clube do Livro — Venda, troque e descubra histórias</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Caveat:wght@500;600;700&family=Bangers&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="<?= assetUrl('assets/styles/home.css') ?>">
</head>
<body>

  <div class="nav-wrap">
    <nav class="navbar" id="navbar">
      <a href="#top" class="nav-logo">
        Clube do Livro
      </a>
      <ul class="nav-links">
        <li><a href="#categorias">Categorias</a></li>
        <li><a href="#recentes">Agora na estante</a></li>
        <li><a href="#como-funciona">Como funciona</a></li>
      </ul>
      <div class="nav-actions">
        <a href="#" class="login-link" data-auth-open="login">Login</a>
        <a href="#" class="btn btn-primary" data-auth-open="cadastro">Entrar</a>
      </div>
      <button class="nav-toggle" aria-label="Abrir menu">☰</button>
    </nav>
  </div>

  <div class="auth-overlay" id="authOverlay">
    <div class="auth-modal">
      <button class="auth-close" id="authClose" aria-label="Fechar">✕</button>
      <span class="eyebrow auth-eyebrow">bem-vindo de volta</span>
      <h2 class="auth-title" id="authTitle">Entrar</h2>

      <div class="auth-switch" id="authSwitch">
        <div class="auth-switch-slider"></div>
        <button type="button" class="is-active" data-auth-tab="login">Login</button>
        <button type="button" data-auth-tab="cadastro">Cadastro</button>
      </div>

      <div class="auth-panels">
        <form class="auth-form is-active" id="loginForm">
          <div class="auth-field">
            <label for="loginEmail">E-mail</label>
            <input type="email" id="loginEmail" placeholder="voce@email.com" required>
          </div>
          <div class="auth-field">
            <label for="loginSenha">Senha</label>
            <div class="auth-password-wrap">
              <input type="password" id="loginSenha" placeholder="••••••••" required>
              <button type="button" class="auth-password-toggle" data-target="loginSenha" aria-label="Mostrar senha"><i class="fa-solid fa-eye"></i></button>
            </div>
          </div>
          <a href="#" class="auth-forgot">Esqueci minha senha</a>
          <button type="submit" class="btn btn-primary auth-submit">Entrar</button>
          <p class="auth-hint">Ainda não tem conta? <button type="button" data-auth-tab="cadastro">Cadastre-se</button></p>
        </form>

        <form class="auth-form" id="cadastroForm" enctype="multipart/form-data">
          <div class="auth-photo">
            <div class="auth-photo-preview" id="cadastroFotoPreview"><i class="fa-solid fa-images"></i></div>
            <label class="auth-photo-label" for="cadastroFoto">Adicionar foto</label>
            <input type="file" id="cadastroFoto" name="foto_perfil" accept="image/*">
          </div>
          <div class="auth-field">
            <label for="cadastroNome">Nome</label>
            <input type="text" id="cadastroNome" name="nome" placeholder="Seu nome" required>
          </div>
          <div class="auth-field">
            <label for="cadastroEmail">E-mail</label>
            <input type="email" id="cadastroEmail" name="email" placeholder="voce@email.com" required>
          </div>
          <div class="auth-field">
            <label for="cadastroTelefone">Telefone</label>
            <input type="tel" id="cadastroTelefone" name="telefone" placeholder="(00) 00000-0000">
          </div>
          <div class="auth-field">
            <label for="cadastroTipo">Você quer</label>
            <select id="cadastroTipo" name="tipo" required>
              <option value="" disabled selected>Selecione uma opção</option>
              <option value="cliente">Ler — quero encontrar e pegar livros</option>
              <option value="colaborador">Vender/Doar — quero disponibilizar meus livros</option>
            </select>
          </div>
          <div class="auth-field">
            <label for="cadastroSenha">Senha</label>
            <div class="auth-password-wrap">
              <input type="password" id="cadastroSenha" name="senha" placeholder="Crie uma senha" required>
              <button type="button" class="auth-password-toggle" data-target="cadastroSenha" aria-label="Mostrar senha"><i class="fa-solid fa-eye"></i></button>
            </div>
            <ul class="auth-password-rules" id="cadastroSenhaRules">
              <li data-rule="length">Mín. 8 caracteres</li>
              <li data-rule="upper">1 letra maiúscula</li>
              <li data-rule="number">1 número</li>
              <li data-rule="special">1 caractere especial</li>
            </ul>
          </div>
          <button type="submit" class="btn btn-primary auth-submit">Criar conta</button>
          <p class="auth-hint">Já tem conta? <button type="button" data-auth-tab="login">Entrar</button></p>
        </form>
      </div>
    </div>
  </div>

  <header class="hero" id="top">
    <div class="hero-inner">
      <img class="hero-book-left" src='<?= base_url('assets/img/pilhaLivros.png'); ?>'>
      <div class="hero-content">
        <h1>Dê nova vida aos seus livros. Venda, troque e descubra novas histórias</h1>
        <p>No Clube do Livro, cada página trocada é uma história que continua viva na estante de outra pessoa.</p>
      </div>
      <img class="hero-book-right" src='<?= base_url('assets/img/livros.png'); ?>'>
    </div>
    <div class="ribbon">
      <div class="ribbon-track">
        <span>Cada livro trocado ganha uma nova história</span>
        <span>Ler é multiplicar histórias</span>
        <span>Seu livro merece outra estante</span>
        <span>Nenhuma história termina, ela só muda de dono</span>
        <span>Cada livro trocado ganha uma nova história</span>
        <span>Ler é multiplicar histórias</span>
        <span>Seu livro merece outra estante</span>
        <span>Nenhuma história termina, ela só muda de dono</span>
      </div>
    </div>
  </header>

  <section class="themes-section" id="categorias">
    <div class="section-head">
      <span class="eyebrow">explore por tema</span>
      <h2>Encontre pelo seu gênero favorito</h2>
    </div>

    <div class="themes-grid">
      <article class="theme-card romance opacity-reveal">
        <div class="theme-body">
          <h2 style="color: var(--pink-deep); -webkit-text-stroke: 1.5px var(--ink); font-size: 50px; letter-spacing: 2px;">Romance</h2>
          <p>Histórias de amor pra fazer o coração disparar a cada capítulo.</p>
          <a href="#" class="btn btn-outline btn-sm theme" style="background-color: #faecdb; color: #2a2313;">Ver todos</a>
        </div>
      </article>

      <article class="theme-card gibis opacity-reveal">
        <div class="theme-body">
          <h2 style="color: var(--red-pow); -webkit-text-stroke: 1.5px var(--ink); font-size: 50px; letter-spacing: 2px;">Gibis</h2>
          <p>Super-heróis, vilões e universos inteiros em cada quadrinho.</p>
          <a href="#" class="btn btn-outline btn-sm theme" style="background-color: #faecdb; color: #2a2313;">Ver todos</a>
        </div>
      </article>

      <article class="theme-card fantasia opacity-reveal">
        <div class="theme-body">
          <h2 style="color: #ee8b09; -webkit-text-stroke: 1.5px var(--ink); font-size: 50px; letter-spacing: 2px;">Fantasia</h2>
          <p>Mundos mágicos, criaturas lendárias e aventuras épicas.</p>
          <a href="#" class="btn btn-outline btn-sm theme" style="background-color: #faecdb; color: #2a2313;">Ver todos</a>
        </div>
      </article>

      <article class="theme-card manga opacity-reveal">
        <div class="theme-body">
          <h2 style="color: var(--olive); -webkit-text-stroke: 1.5px var(--ink); font-size: 50px; letter-spacing: 2px;">Mangá</h2>
          <p>Traços marcantes e histórias direto do Japão pra sua estante.</p>
          <a href="#" class="btn btn-outline btn-sm theme" style="background-color: #faecdb; color: #2a2313;">Ver todos</a>
        </div>
      </article>
    </div>

    <div class="book-cta-btn">
      <span class="mas">Ver todos</span>
      <button id="workThemes" type="button" name="Hover">Ver todos</button>
    </div>
  </section>

  <section class="recent-books-section" id="recentes">
    <div class="section-head" style="margin-bottom: 60px; margin-top: -50px;">
      <span class="eyebrow" style="font-size: 2rem;">novidades</span>
      <h2 style="color: var(--ink); -webkit-text-stroke: 1px var(--ink); letter-spacing: 3px; font-size: 3.5rem;">Chegou agora na estante</h2>
    </div>

    <div class="books-grid">
      <article class="book-card opacity-reveal">
        <span class="book-tag tag-troca">Troca</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>A Menina que Roubava Livros</h3>
          <p class="book-author">Markus Zusak</p>
          <div class="book-meta">
            <span class="book-price only-troca">Somente troca</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-venda">Venda</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>O Nome do Vento</h3>
          <p class="book-author">Patrick Rothfuss</p>
          <div class="book-meta">
            <span class="book-price">R$ 32,00</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-ambos">Troca ou venda</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>Duna</h3>
          <p class="book-author">Frank Herbert</p>
          <div class="book-meta">
            <span class="book-price">R$ 28,00</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-venda">Venda</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>Homem-Aranha: De Volta ao Lar</h3>
          <p class="book-author">Marvel Comics</p>
          <div class="book-meta">
            <span class="book-price">R$ 18,00</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-troca">Troca</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>Orgulho e Preconceito</h3>
          <p class="book-author">Jane Austen</p>
          <div class="book-meta">
            <span class="book-price only-troca">Somente troca</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-ambos">Troca ou venda</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>1984</h3>
          <p class="book-author">George Orwell</p>
          <div class="book-meta">
            <span class="book-price">R$ 22,00</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-venda">Venda</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>X-Men: Dias de um Futuro Esquecido</h3>
          <p class="book-author">Marvel Comics</p>
          <div class="book-meta">
            <span class="book-price">R$ 15,00</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>

      <article class="book-card opacity-reveal">
        <span class="book-tag tag-troca">Troca</span>
        <div class="book-cover"></div>
        <div class="book-info">
          <h3>O Guia do Mochileiro das Galáxias</h3>
          <p class="book-author">Douglas Adams</p>
          <div class="book-meta">
            <span class="book-price only-troca">Somente troca</span>
            <a href="#" class="btn btn-outline">Ver</a>
          </div>
        </div>
      </article>
    </div>

    <div class="book-cta-btn">
      <span class="mas">Ver todos</span>
      <button id="workBooks" type="button" name="Hover">Ver todos</button>
    </div>
  </section>

  <section class="timeline-section" id="como-funciona">
    <div class="section-head">
      <span class="eyebrow">como funciona</span>
      <h2>Do seu armário para uma nova aventura</h2>
    </div>

    <div class="timeline">
      <div class="step opacity-reveal">
        <div class="step-card">
          <h3>01 — Cadastre</h3>
          <p>Tire uma foto e coloque seu livro ou gibi no catálogo.</p>
        </div>
        <div class="step-badge">01</div>
      </div>
      <div class="step opacity-reveal">
        <div class="step-card">
          <h3>02 — Escolha</h3>
          <p>Decida se quer trocar, vender ou fazer os dois.</p>
        </div>
        <div class="step-badge">02</div>
      </div>
      <div class="step opacity-reveal">
        <div class="step-card">
          <h3>03 — Encontre</h3>
          <p>Outra pessoa encontra sua história.</p>
        </div>
        <div class="step-badge">03</div>
      </div>
      <div class="step opacity-reveal">
        <div class="step-card">
          <h3>04 — Combine</h3>
          <p>Converse pelo chat e combine a troca ou venda.</p>
        </div>
        <div class="step-badge">04</div>
      </div>
      <div class="step opacity-reveal">
        <div class="step-card">
          <h3>05 — Leia</h3>
          <p>Seu livro começa uma nova aventura.</p>
        </div>
        <div class="step-badge">05</div>
      </div>
    </div>
    <div class="buttons">
      <button class="blob-btn">
        Trocar / Vender
        <span class="blob-btn__inner">
          <span class="blob-btn__blobs">
            <span class="blob-btn__blob"></span>
            <span class="blob-btn__blob"></span>
            <span class="blob-btn__blob"></span>
            <span class="blob-btn__blob"></span>
          </span>
        </span>
      </button>
      <br/>

    <svg xmlns="http://www.w3.org/2000/svg" version="1.1">
      <defs>
        <filter id="goo">
          <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
          <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
          <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
        </filter>
      </defs>
    </svg>
  </section>

  <div class="footer-wave">
    <svg 
        viewBox="0 0 1440 120" 
        preserveAspectRatio="none"
        xmlns="http://www.w3.org/2000/svg">
        <path 
            fill="var(--cream)"
            d="M0,70 
               C180,10 300,10 480,65
               S780,120 960,65
               S1260,10 1440,70
               L1440,120
               L0,120
               Z">
        </path>
      </svg>
  </div>

  <footer>
    <div class="footer-bottom">
      <span>© 2026 Clube do Livro. Daniel S., Daniel Q., Gleicekelly, Silmara, Thaise</span>
    </div>
  </footer>

  <script>
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 30);
    });

    const revealEls = document.querySelectorAll('.opacity-reveal');
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach((el) => revealObserver.observe(el));

    const toggle = document.querySelector('.nav-toggle');
    const links = document.querySelector('.nav-links');

    toggle.addEventListener('click', function () {
        links.classList.toggle('nav-open');
    });

    document.getElementById('workThemes').addEventListener('click', () => {
      window.location.href = '#';
    });
    document.getElementById('workBooks').addEventListener('click', () => {
      window.location.href = '#';
    });

    const authOverlay = document.getElementById('authOverlay');
    const authSwitch = document.getElementById('authSwitch');
    const authTitle = document.getElementById('authTitle');
    const authClose = document.getElementById('authClose');
    const loginForm = document.getElementById('loginForm');
    const cadastroForm = document.getElementById('cadastroForm');

    function openAuth(mode) {
      authOverlay.classList.add('is-open');
      document.body.classList.add('modal-open');
      setAuthMode(mode || 'login');
    }

    function closeAuth() {
      authOverlay.classList.remove('is-open');
      document.body.classList.remove('modal-open');
    }

    function setAuthMode(mode) {
      const isCadastro = mode === 'cadastro';
      authSwitch.classList.toggle('mode-cadastro', isCadastro);
      authTitle.textContent = isCadastro ? 'Criar conta' : 'Entrar';

      authSwitch.querySelectorAll('button[data-auth-tab]').forEach((btn) => {
        btn.classList.toggle('is-active', btn.dataset.authTab === mode);
      });

      loginForm.classList.toggle('is-active', !isCadastro);
      cadastroForm.classList.toggle('is-active', isCadastro);
    }

    document.querySelectorAll('[data-auth-open]').forEach((el) => {
      el.addEventListener('click', (event) => {
        event.preventDefault();
        openAuth(el.dataset.authOpen);
      });
    });

    document.querySelectorAll('[data-auth-tab]').forEach((el) => {
      el.addEventListener('click', () => setAuthMode(el.dataset.authTab));
    });

    authClose.addEventListener('click', closeAuth);

    authOverlay.addEventListener('click', (event) => {
      if (event.target === authOverlay) {
        closeAuth();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && authOverlay.classList.contains('is-open')) {
        closeAuth();
      }
    });

    loginForm.addEventListener('submit', (event) => {
      event.preventDefault();
      closeAuth();
    });

    const cadastroFoto = document.getElementById('cadastroFoto');
    const cadastroFotoPreview = document.getElementById('cadastroFotoPreview');

    cadastroFoto.addEventListener('change', () => {
      const file = cadastroFoto.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = (e) => {
        cadastroFotoPreview.innerHTML = `<img src="${e.target.result}" alt="Prévia da foto">`;
      };
      reader.readAsDataURL(file);
    });

    document.querySelectorAll('.auth-password-toggle').forEach((btn) => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const mostrando = input.type === 'text';
        input.type = mostrando ? 'password' : 'text';
        btn.innerHTML = mostrando ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
      });
    });

    const cadastroSenha = document.getElementById('cadastroSenha');
    const senhaRules = document.getElementById('cadastroSenhaRules');

    const regrasSenha = {
      length:  (v) => v.length >= 8,
      upper:   (v) => /[A-Z]/.test(v),
      number:  (v) => /[0-9]/.test(v),
      special: (v) => /[^A-Za-z0-9]/.test(v),
    };

    function senhaEhValida(valor) {
      return Object.values(regrasSenha).every((teste) => teste(valor));
    }

    cadastroSenha.addEventListener('input', () => {
      const valor = cadastroSenha.value;
      Object.entries(regrasSenha).forEach(([regra, teste]) => {
        senhaRules.querySelector(`[data-rule="${regra}"]`).classList.toggle('valid', teste(valor));
      });
    });

    cadastroForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      if (!senhaEhValida(cadastroSenha.value)) {
        cadastroSenha.focus();
        return;
      }

      const formData = new FormData(cadastroForm);
      const submitBtn = cadastroForm.querySelector('.auth-submit');
      submitBtn.disabled = true;

      try {
        const resp = await fetch('<?= base_url('cadastro') ?>', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await resp.json();

        if (data.success) {
          closeAuth();
        } else {
          console.log(data.errors);
        }
      } catch (err) {
        console.error(err);
      } finally {
        submitBtn.disabled = false;
      }
    });

    const cadastroTelefone = document.getElementById('cadastroTelefone');

    cadastroTelefone.addEventListener('input', () => {
      let valor = cadastroTelefone.value.replace(/\D/g, '').slice(0, 11);

      if (valor.length > 10) {
        valor = valor.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
      } else if (valor.length > 6) {
        valor = valor.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
      } else if (valor.length > 2) {
        valor = valor.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
      } else if (valor.length > 0) {
        valor = valor.replace(/^(\d{0,2})/, '($1');
      }

      cadastroTelefone.value = valor;
    });
  </script>
</body>
</html>