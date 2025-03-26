// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Preloader
    window.addEventListener("load", () => {
      const preloader = document.querySelector(".preloader")
      preloader.classList.add("fade-out")
      setTimeout(() => {
        preloader.style.display = "none"
      }, 500)
    })
  
    // Header scroll effect
    const header = document.getElementById("header")
    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        header.classList.add("scrolled")
      } else {
        header.classList.remove("scrolled")
      }
    })
  
    // Mobile Menu Toggle
    const menuToggle = document.querySelector(".menu-toggle")
    const navMenu = document.querySelector(".nav-menu")
  
    menuToggle.addEventListener("click", () => {
      navMenu.classList.toggle("active")
      menuToggle.classList.toggle("active")
  
      if (menuToggle.classList.contains("active")) {
        menuToggle.innerHTML = '<i class="fas fa-times"></i>'
      } else {
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>'
      }
    })
  
    // Close mobile menu when clicking on a nav link
    const navLinks = document.querySelectorAll(".nav-menu a")
    navLinks.forEach((link) => {
      link.addEventListener("click", () => {
        navMenu.classList.remove("active")
        menuToggle.classList.remove("active")
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>'
      })
    })
  
    // Hero Slider
    const slides = document.querySelectorAll(".slide")
    const dots = document.querySelectorAll(".slider-dots .dot")
    const prevBtn = document.querySelector(".prev-btn")
    const nextBtn = document.querySelector(".next-btn")
    let currentSlide = 0
    let slideInterval
  
    function showSlide(index) {
      slides.forEach((slide) => slide.classList.remove("active"))
      dots.forEach((dot) => dot.classList.remove("active"))
  
      slides[index].classList.add("active")
      dots[index].classList.add("active")
      currentSlide = index
    }
  
    function nextSlide() {
      currentSlide = (currentSlide + 1) % slides.length
      showSlide(currentSlide)
    }
  
    function prevSlide() {
      currentSlide = (currentSlide - 1 + slides.length) % slides.length
      showSlide(currentSlide)
    }
  
    // Initialize slider and autoplay
    function startSlider() {
      slideInterval = setInterval(nextSlide, 6000)
    }
  
    function stopSlider() {
      clearInterval(slideInterval)
    }
  
    // Event listeners for slider controls
    prevBtn.addEventListener("click", () => {
      prevSlide()
      stopSlider()
      startSlider()
    })
  
    nextBtn.addEventListener("click", () => {
      nextSlide()
      stopSlider()
      startSlider()
    })
  
    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        showSlide(index)
        stopSlider()
        startSlider()
      })
    })
  
    // Start the slider
    startSlider()
  
    // Testimonial Slider
    const testimonialSlides = document.querySelectorAll(".testimonial-slide")
    const testimonialDots = document.querySelectorAll(".testimonial-dots .dot")
    const testimonialPrev = document.querySelector(".testimonial-prev")
    const testimonialNext = document.querySelector(".testimonial-next")
    let currentTestimonial = 0
  
    function showTestimonial(index) {
      testimonialSlides.forEach((slide) => slide.classList.remove("active"))
      testimonialDots.forEach((dot) => dot.classList.remove("active"))
  
      testimonialSlides[index].classList.add("active")
      testimonialDots[index].classList.add("active")
      currentTestimonial = index
    }
  
    function nextTestimonial() {
      currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length
      showTestimonial(currentTestimonial)
    }
  
    function prevTestimonial() {
      currentTestimonial = (currentTestimonial - 1 + testimonialSlides.length) % testimonialSlides.length
      showTestimonial(currentTestimonial)
    }
  
    // Event listeners for testimonial controls
    testimonialPrev.addEventListener("click", prevTestimonial)
    testimonialNext.addEventListener("click", nextTestimonial)
  
    testimonialDots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        showTestimonial(index)
      })
    })
  
    // Auto change testimonial every 8 seconds
    setInterval(nextTestimonial, 8000)
  
    // Counter Animation
    const counters = document.querySelectorAll(".counter")
    const speed = 200
  
    function animateCounters() {
      counters.forEach((counter) => {
        const target = +counter.getAttribute("data-count")
        const count = +counter.innerText
        const increment = target / speed
  
        if (count < target) {
          counter.innerText = Math.ceil(count + increment)
          setTimeout(animateCounters, 1)
        } else {
          counter.innerText = target
        }
      })
    }
  
    // Scroll Reveal Animation
    function revealElements() {
      const reveals = document.querySelectorAll(".reveal-left, .reveal-right, .reveal-top, .reveal-bottom, .reveal-item")
  
      reveals.forEach((element) => {
        const windowHeight = window.innerHeight
        const elementTop = element.getBoundingClientRect().top
        const elementVisible = 150
  
        if (elementTop < windowHeight - elementVisible) {
          // Add delay if specified
          const delay = element.getAttribute("data-delay")
          if (delay) {
            setTimeout(() => {
              element.classList.add("active")
            }, delay)
          } else {
            element.classList.add("active")
          }
        }
      })
    }
  
    // Back to Top Button
    const backToTopBtn = document.getElementById("backToTop")
  
    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        backToTopBtn.classList.add("active")
      } else {
        backToTopBtn.classList.remove("active")
      }
  
      // Call reveal function on scroll
      revealElements()
  
      // Start counter animation when in view
      const counterSection = document.querySelector(".counter-section")
      if (counterSection) {
        const sectionTop = counterSection.getBoundingClientRect().top
        const windowHeight = window.innerHeight
  
        if (sectionTop < windowHeight - 100 && sectionTop > -counterSection.offsetHeight) {
          animateCounters()
        }
      }
    })
  
    backToTopBtn.addEventListener("click", (e) => {
      e.preventDefault()
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
  
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        if (this.getAttribute("href") !== "#") {
          e.preventDefault()
          const target = document.querySelector(this.getAttribute("href"))
          if (target) {
            window.scrollTo({
              top: target.offsetTop - 80,
              behavior: "smooth",
            })
          }
        }
      })
    })
  
    // Active navigation link on scroll
    function activeNavLink() {
      const sections = document.querySelectorAll("section")
      const navLinks = document.querySelectorAll(".nav-menu a")
  
      let current = ""
  
      sections.forEach((section) => {
        const sectionTop = section.offsetTop
        const sectionHeight = section.clientHeight
  
        if (window.scrollY >= sectionTop - 100) {
          current = section.getAttribute("id")
        }
      })
  
      navLinks.forEach((link) => {
        link.classList.remove("active")
        if (link.getAttribute("href") === `#${current}`) {
          link.classList.add("active")
        }
      })
    }
  
    window.addEventListener("scroll", activeNavLink)
  
    // Gallery Lightbox
    const galleryLinks = document.querySelectorAll(".gallery-link")
    const lightbox = document.getElementById("lightbox")
    const lightboxImg = document.getElementById("lightbox-img")
    const closeLightbox = document.querySelector(".close-lightbox")
  
    galleryLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault()
        const imgSrc = this.getAttribute("href")
        lightboxImg.src = imgSrc
        lightbox.style.display = "flex"
        document.body.style.overflow = "hidden"
      })
    })
  
    closeLightbox.addEventListener("click", () => {
      lightbox.style.display = "none"
      document.body.style.overflow = "auto"
    })
  
    lightbox.addEventListener("click", (e) => {
      if (e.target === lightbox) {
        lightbox.style.display = "none"
        document.body.style.overflow = "auto"
      }
    })
  
    // Contact Form Submission
    const contactForm = document.getElementById("contactForm")
    if (contactForm) {
      contactForm.addEventListener("submit", (e) => {
        e.preventDefault()
  
        // Get form values
        const name = document.getElementById("name").value
        const email = document.getElementById("email").value
        const subject = document.getElementById("subject").value
        const message = document.getElementById("message").value
  
        // Simple validation
        if (name && email && subject && message) {
          // Simulate form submission (replace with actual form submission)
          const submitBtn = contactForm.querySelector('button[type="submit"]')
          const originalText = submitBtn.innerText
  
          submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...'
          submitBtn.disabled = true
  
          setTimeout(() => {
            // Reset form
            contactForm.reset()
  
            // Show success message
            const formCard = document.querySelector(".form-card")
            formCard.innerHTML = `
                          <div class="text-center">
                              <i class="fas fa-check-circle" style="font-size: 60px; color: var(--primary); margin-bottom: 20px;"></i>
                              <h3>Thank You!</h3>
                              <p class="mb-4">Your message has been sent successfully. We'll get back to you soon!</p>
                              <button class="btn btn-primary" onclick="location.reload()">Send Another Message</button>
                          </div>
                      `
          }, 2000)
        }
      })
    }
  
    // Initialize animations on page load
    revealElements()
  })
  
  