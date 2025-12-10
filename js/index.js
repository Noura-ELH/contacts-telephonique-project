document.addEventListener("DOMContentLoaded", () => {
  // ======= Slider =======
  const slides = document.querySelectorAll('.slide');
  let index = 0;

  if (slides.length > 0) {
    setInterval(() => {
      slides[index].classList.remove('active');
      index = (index + 1) % slides.length;
      slides[index].classList.add('active');
    }, 2000);
  }

  // ======= Features =======
  const featuresData = [
    {
      icon: "fa-solid fa-gears",
      title: "Easy Management",
      desc: "Organize your contacts quickly and intuitively."
    },
    {
      icon: "fa-solid fa-display",
      title: "Smart Interface",
      desc: "User-friendly design that requires no learning curve."
    },
    {
      icon: "fa-solid fa-shield-halved",
      title: "Secure Data",
      desc: "We prioritize your privacy and data protection."
    },
    {
      icon: "fa-solid fa-eye",
      title: "Live Preview",
      desc: "See how features rotate every 4 seconds in action."
    }
  ];

  const featuresContainer = document.getElementById("features-container");
  let currentStartIndex = 0;

  function renderFeatures(startIndex) {
    if (!featuresContainer) return;
    featuresContainer.innerHTML = "";

    for (let i = 0; i < 3; i++) {
      const index = (startIndex + i) % featuresData.length;
      const feature = featuresData[index];

      const div = document.createElement("div");
      div.className = "card show";
      div.innerHTML = `
        <i class="${feature.icon}" style="font-size: 32px; color: #fff; margin-bottom: 10px;"></i>
        <h3>${feature.title}</h3>
        <p>${feature.desc}</p>
      `;
      featuresContainer.appendChild(div);
    }
  }

  renderFeatures(currentStartIndex);

  if (featuresContainer) {
    setInterval(() => {
      currentStartIndex = (currentStartIndex + 1) % featuresData.length;
      renderFeatures(currentStartIndex);
    }, 2000);
  }

  // ======= Navbar sticky =======
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 100) {
        navbar.classList.add('sticky');
      } else {
        navbar.classList.remove('sticky');
      }
    });
  }

  // ======= Mobile menu =======
  const navLinks = document.querySelector(".nav-links");
  const mobileBtn = document.getElementById("mobileMenu");
  if (mobileBtn && navLinks) {
    mobileBtn.addEventListener("click", () => {
      navLinks.classList.toggle("show");
    });
  }

  // ======= Dashboard =======
  const toggleTags = () => {
    const list = document.getElementById('tagsList');
    if (list) {
      list.style.display = list.style.display === 'none' ? 'block' : 'none';
    }
  };

  const filterBy = (tag) => {
    const contacts = document.querySelectorAll('.contact-card');
    contacts.forEach(card => {
      if (card.dataset.tag) {
        card.style.display = (tag === 'All' || card.dataset.tag === tag) ? 'flex' : 'none';
      }
    });
  };

  const openModal = () => {
    const modal = document.getElementById('addContactModal');
    if (modal) modal.style.display = 'block';
  };

  const closeModal = () => {
    const modal = document.getElementById('addContactModal');
    if (modal) modal.style.display = 'none';
  };

  const openDetails = (name, tag, email, phone, image, id) => {
    const detailName = document.getElementById('detailName');
    const detailTag = document.getElementById('detailTag');
    const detailEmail = document.getElementById('detailEmail');
    const detailPhone = document.getElementById('detailPhone');
    const detailRelation = document.getElementById('detailRelation');
    const imagecontact = document.getElementById('imagecontact');
    const modifyLink = document.getElementById('modifyLink');
    const deleteLink = document.getElementById('deleteLink');
    const detailsModal = document.getElementById('detailsModal');

    if (detailName) detailName.textContent = name;
    if (detailTag) detailTag.textContent = tag;
    if (detailEmail) detailEmail.textContent = email;
    if (detailPhone) detailPhone.textContent = phone;
    if (detailRelation) detailRelation.textContent = tag;
    if (imagecontact) imagecontact.src = `uploads/${image}`;
    if (modifyLink) modifyLink.href = `modify.php?id=${id}`;
    if (deleteLink) deleteLink.href = `remove.php?id=${id}`;
    if (detailsModal) detailsModal.style.display = 'flex';
  };

  const closeDetails = () => {
    const detailsModal = document.getElementById('detailsModal');
    if (detailsModal) detailsModal.style.display = 'none';
  };

  // ======= Search =======
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.oninput = () => {
      document.querySelectorAll(".contact-card").forEach(card => {
        const name = card.querySelector("h3")?.textContent.toLowerCase() || "";
        card.style.display = name.includes(searchInput.value.toLowerCase()) ? "flex" : "none";
      });
    };
  }

  // ======= Theme switch =======
  const toggleBtn = document.getElementById('themeToggle');
  const body = document.body;
  if (toggleBtn && body) {
    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      body.classList.add(savedTheme);
      toggleBtn.innerHTML = savedTheme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }

    toggleBtn.addEventListener('click', () => {
      if (body.classList.contains('dark')) {
        body.classList.replace('dark', 'light');
        localStorage.setItem('theme', 'light');
        toggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
      } else {
        body.classList.replace('light', 'dark');
        localStorage.setItem('theme', 'dark');
        toggleBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
      }
    });
  }

  // ======= Expose dashboard functions globally =======
  window.toggleTags = toggleTags;
  window.filterBy = filterBy;
  window.openModal = openModal;
  window.closeModal = closeModal;
  window.openDetails = openDetails;
  window.closeDetails = closeDetails;
});
