<?php
require_once './config.php';
$conn = db_connect();
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faith Baptist Church - Abeyee</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader"></div>
    </div> 

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Header -->
    <header id="header">
        <div class="container">
            <div class="logo">
                <a href="#">
                    <img src="./images/logo.jpg" alt="Faith Baptist Church Logo" width="50" height="50"
                        style="margin-right: 10px; vertical-align: middle;">
                    Faith Baptist <span>Church</span>
                </a>
            </div>
            <nav>
                <div class="menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
                <ul class="nav-menu">
                    <li><a href="#home" class="active">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="ministries.html">Ministries</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#sermons">Sermons</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="donate.html" class="donate-btn">Donate</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="slider">
            <div class="slide active">
                <div class="slide-bg" style="background-image: url('./images/group-pic-1.jpg')"></div>
                <div class="overlay"></div>
                <div class="container">
                    <div class="slide-content">
                        <h6 class="fade-in">Welcome to Faith Baptist Church</h6>
                        <h1 class="fade-in">A Place of Faith, Hope & Love</h1>
                        <p class="fade-in">Join our community and experience the love of Christ in a welcoming
                            environment.</p>
                        <div class="buttons fade-in">
                            <a href="#services" class="btn btn-primary">Join Us Sunday</a>
                            <a href="#about" class="btn btn-outline">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide">
                <div class="slide-bg" style="background-image: url('./images/group-pic-2.jpg')"></div>
                <div class="overlay"></div>
                <div class="container">
                    <div class="slide-content">
                        <h6 class="fade-in">Worship With Us</h6>
                        <h1 class="fade-in">Experience Meaningful Worship</h1>
                        <p class="fade-in">Our services are designed to connect you with God and our community.</p>
                        <div class="buttons fade-in">
                            <a href="#services" class="btn btn-primary">Service Times</a>
                            <a href="#contact" class="btn btn-outline">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slide">
                <div class="slide-bg" style="background-image: url('./images/church-pic-1.jpg')"></div>
                <div class="overlay"></div>
                <div class="container">
                    <div class="slide-content">
                        <h6 class="fade-in">Our Community</h6>
                        <h1 class="fade-in">Growing Together in Faith</h1>
                        <p class="fade-in">Find your place in our diverse and welcoming church family.</p>
                        <div class="buttons fade-in">
                            <a href="#events" class="btn btn-primary">Upcoming Events</a>
                            <a href="#gallery" class="btn btn-outline">View Gallery</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-controls">
                <button class="prev-btn"><i class="fas fa-chevron-left"></i></button>
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                </div>
                <button class="next-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about section-padding">
        <div class="container">
            <div class="row">
                <div class="about-img reveal-left">
                    <div class="img-wrapper">
                        <img src="./images/pastor-and-wife.jpg" alt="About Our Church">
                        <div class="stats-card">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <h3 class="counter" data-count="35">0</h3>
                                    <p>Years of Service</p>
                                </div>
                                <div class="stat-item">
                                    <h3 class="counter" data-count="2500">0</h3>
                                    <p>Community Members</p>
                                </div>
                                <div class="stat-item">
                                    <h3 class="counter" data-count="50">0</h3>
                                    <p>Ministries</p>
                                </div>
                                <div class="stat-item">
                                    <h3 class="counter" data-count="120">0</h3>
                                    <p>Events Yearly</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="about-content reveal-right">
                    <h6>About Us</h6>
                    <h2>A Church That's Making a Difference</h2>
                    <p class="lead">Faith Baptist Church is a vibrant community of believers dedicated to spreading
                        God's love and message of hope to all people.</p>
                    <p>Founded in 1985, our church has been a cornerstone of the Abeyee community for over 35 years. We
                        believe in creating an inclusive environment where everyone can experience God's presence and
                        grow in their faith journey.</p>
                    <div class="features">
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Inclusive Community</h5>
                                <p>Everyone is welcome in our diverse church family.</p>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Biblical Teaching</h5>
                                <p>Grounded in scripture and relevant to daily life.</p>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Community Service</h5>
                                <p>Actively serving our local and global community.</p>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-content">
                                <h5>Family Focused</h5>
                                <p>Programs for all ages from children to seniors.</p>
                            </div>
                        </div>
                    </div>
                    <div class="buttons">
                        <a href="#contact" class="btn btn-primary">Contact Us</a>
                        <a href="#services" class="btn btn-outline">Our Services</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services section-padding bg-light">
        <div class="container">
            <div class="section-header text-center">
                <h6 class="reveal-top">Worship With Us</h6>
                <h2 class="reveal-top">Weekly Services</h2>
                <p class="reveal-top">Join us for worship and fellowship throughout the week</p>
            </div>
            <div class="services-grid">
                <div class="service-card reveal-item">
                    <div class="service-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Sunday Worship</h4>
                    <p>Join us every Sunday for our main worship service with music, prayer, and teaching.</p>
                    <div class="service-info">
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>8:00 AM - 11:00 AM</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Main Sanctuary</span>
                        </div>
                    </div>
                </div>
                <div class="service-card reveal-item" data-delay="200">
                    <div class="service-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>Wednesday Bible Study</h4>
                    <p>Midweek Bible study and prayer meeting for spiritual growth and fellowship.</p>
                    <div class="service-info">
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>7:00 PM - 9:00 PM</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Fellowship Hall</span>
                        </div>
                    </div>
                </div>
                <div class="service-card reveal-item" data-delay="400">
                    <div class="service-icon">
                        <i class="fas fa-pray"></i>
                    </div>
                    <h4>Friday Prayer Revival</h4>
                    <p>Weekly prayer service focused on intercession and spiritual renewal.</p>
                    <div class="service-info">
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>7:00 PM - 9:00 PM</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Prayer Room</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Counter Section -->
    <section class="counter-section">
        <div class="overlay"></div>
        <div class="container">
            <div class="counter-grid">
                <div class="counter-item reveal-item">
                    <i class="fas fa-users"></i>
                    <h2 class="counter" data-count="2500">0</h2>
                    <p>Church Members</p>
                </div>
                <div class="counter-item reveal-item" data-delay="200">
                    <i class="fas fa-heart"></i>
                    <h2 class="counter" data-count="150">0</h2>
                    <p>Volunteers</p>
                </div>
                <div class="counter-item reveal-item" data-delay="400">
                    <i class="fas fa-calendar-check"></i>
                    <h2 class="counter" data-count="120">0</h2>
                    <p>Events Per Year</p>
                </div>
                <div class="counter-item reveal-item" data-delay="600">
                    <i class="fas fa-globe"></i>
                    <h2 class="counter" data-count="35">0</h2>
                    <p>Years of Service</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
   


 <section id="events" class="events section-padding">
        <div class="container">
            <div class="section-header text-center">
                <h6 class="reveal-top">UPCOMING</h6>
                <h2 class="reveal-top">Events & Activities</h2>
                <p class="reveal-top">Join us for these special events in our community</p>
            </div>
            <div class="events-grid">
            <?php
            $sql = "SELECT * FROM events ORDER BY event_date DESC LIMIT 3";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="event-card reveal-item">';
                    echo '    <div class="event-img">';
                    echo '        <img src="./uploads/events/'.$row['image'].'" alt="Event">';
                    echo '        <div class="event-date">';
                    echo '            <span class="day">' . date("d", strtotime($row["event_date"])) . '</span>';
                    echo '            <span class="month">' . date("M", strtotime($row["event_date"])) . '</span>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '    <div class="event-content">';
                    echo '        <h4>' . $row["title"] . '</h4>';
                    echo '        <p>' . $row["description"] . '</p>';
                    echo '        <div class="event-info">';
                    echo '            <div class="info-item">';
                    echo '                <i class="fas fa-clock"></i>';
                    echo '                <span>' . $row["event_time"] . '</span>';
                    echo '            </div>';
                    echo '            <div class="info-item">';
                    echo '                <i class="fas fa-map-marker-alt"></i>';
                    echo '                <span>' . $row["location"] . '</span>';
                    echo '            </div>';
                    echo '        </div>';
                    echo '        <a href="#" class="btn btn-outline">Learn More</a>';
                    echo '    </div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No events found.</p>';
            }
            
            ?>
        </div>
        <div class="text-center mt-50 reveal-bottom">
            <a href="#" class="btn btn-outline">View All Events</a>
        </div>
    </div>
</section>




    <!-- Sermons Section -->
    <section id="sermons" class="sermons section-padding bg-light">
        <div class="container">
            <div class="section-header text-center">
                <h6 class="reveal-top">LISTEN & WATCH</h6>
                <h2 class="reveal-top">Recent Sermons</h2>
                <p class="reveal-top">Missed a service? Catch up with our recent messages</p>
            </div>
            <div class="sermons-grid">
                <?php
                if ($conn->connect_error) {
                    echo '<p>An error occurred. Please try again later.</p>';
                } else {
                    $sql = "SELECT * FROM sermons ORDER BY sermon_date DESC LIMIT 3";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="sermon-card reveal-item">';
                            echo '    <div class="sermon-img">';
                            echo '        <img src="./uploads/sermons/images/'.$row['image'].' " alt="Sermon">';
                            echo '        <a href="#" class="play-btn sermon-play" data-audio="./uploads/sermons/audio/' . $row['audio_file'] . '" data-title="' . $row["title"] . '">';
                            echo '            <i class="fas fa-play"></i>';
                            echo '        </a>';
                            echo '    </div>';
                            echo '    <div class="sermon-content">';
                            echo '        <div class="sermon-date">';
                            echo '            <i class="fas fa-calendar"></i>';
                            echo '            <span>' . date("F j, Y", strtotime($row["sermon_date"])) . '</span>';
                            echo '        </div>';
                            echo '        <h4>' . $row["title"] . '</h4>';
                            echo '        <p>' . $row["description"] . '</p>';
                            echo '        <div class="sermon-links">';
                            echo '            <a href="#" class="sermon-listen" data-audio="./uploads/sermons/audio/'. $row['audio_file'] . '" data-title="' . $row["title"] . '">';
                            echo '                <i class="fas fa-headphones"></i>';
                            echo '                <span>Listen</span>';
                            echo '            </a>';
                            echo '            <a href="./uploads/sermons/pdfs/'.$row['audio_file'].'" download>';
                            echo '                <i class="fas fa-download"></i>';
                            echo '                <span>Download</span>';
                            echo '            </a>';
                            echo '            <a href="#" class="sermon-share" data-title="' . $row["title"] . '" data-url="./uploads/sermons/pdfs/' . $row['image'] . '">';
                            echo '                <i class="fas fa-share"></i>';
                            echo '                <span>Share</span>';
                            echo '            </a>';
                            echo '        </div>';
                            echo '    </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No sermons found.</p>';
                    }
             
                }
                ?>
            </div> 
                   
            <div class="text-center mt-50 reveal-bottom">
                <a href="sermons.html" class="btn btn-outline">View All Sermons</a>
            </div>
        </div>
    </section>










    <!-- Testimonials Section -->
    <section class="testimonials section-padding">
        <div class="container">
            <div class="section-header text-center">
                <h6 class="reveal-top">TESTIMONIALS</h6>
                <h2 class="reveal-top">What People Say</h2>
                <p class="reveal-top">Hear from members of our church community</p>
            </div>
            <div class="testimonial-slider reveal-item">
                <div class="testimonial-track">
                    <div class="testimonial-slide active">
                        <div class="testimonial-card">
                            <img src="./images/linda.jpg" alt="Testimonial">
                            <h4>Sarah Thompson</h4>
                            <p class="position">Church Member for 5 Years</p>
                            <p class="quote">"Finding Faith Baptist Church was a true blessing in my life. The community
                                here has supported me through difficult times and celebrated with me during joyous
                                occasions. The teachings are relevant and have helped me grow in my faith journey."</p>
                        </div>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-card">
                            <img src="./images/quamina.jpg" alt="Testimonial">
                            <h4>Michael Rodriguez</h4>
                            <p class="position">Youth Group Leader</p>
                            <p class="quote">"I've been attending Faith Baptist Church for over 10 years, and it's been
                                amazing to see how the church has grown while maintaining its welcoming atmosphere. The
                                youth ministry has been particularly impactful, providing a safe space for teenagers to
                                explore their faith."</p>
                        </div>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-card">
                            <img src="./images/saviour.jpg" alt="Testimonial">
                            <h4>The Johnson Family</h4>
                            <p class="position">New Members</p>
                            <p class="quote">"As newcomers to the area, we were looking for a church that offered
                                programs for our entire family. Faith Baptist Church exceeded our expectations with its
                                children's ministry, adult Bible studies, and community events. We immediately felt at
                                home and connected."</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-controls">
                    <button class="testimonial-prev"><i class="fas fa-chevron-left"></i></button>
                    <div class="testimonial-dots">
                        <span class="dot active" data-slide="0"></span>
                        <span class="dot" data-slide="1"></span>
                        <span class="dot" data-slide="2"></span>
                    </div>
                    <button class="testimonial-next"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section-padding bg-light">
        <div class="container">
            <div class="section-header text-center">
                <h6 class="reveal-top">OUR GALLERY</h6>
                <h2 class="reveal-top">Moments from Our Church</h2>
                <p class="reveal-top">Browse through images of our services, events, and community</p>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item reveal-item">
                    <img src="./images/pastor-and-others.jpg" alt="Church Gallery">
                    <div class="gallery-overlay">
                        <a href="./images/pastor-and-others.jpg" class="gallery-link" data-lightbox="gallery">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="gallery-item reveal-item" data-delay="100">
                    <img src="./images/sunday-school-group-pic.jpg" alt="Church Gallery">
                    <div class="gallery-overlay">
                        <a href="./images/sunday-school-group-pic.jpg" class="gallery-link" data-lightbox="gallery">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="gallery-item reveal-item" data-delay="200">
                    <img src="./images/radio-presentatioj.jpg" alt="Church Gallery">
                    <div class="gallery-overlay">
                        <a href="./images/radio-presentatioj.jpg" class="gallery-link" data-lightbox="gallery">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="gallery-item reveal-item" data-delay="100">
                    <img src="./images/baptism-1.jpg" alt="Not yet">
                    <div class="gallery-overlay">
                        <a href="./images/baptism-1.jpg" class="gallery-link" data-lightbox="gallery">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="gallery-item reveal-item" data-delay="200">
                    <img src="./images/sunday-school-1.jpg" alt="Church Gallery">
                    <div class="gallery-overlay">
                        <a href="./images/sunday-school-1.jpg" class="gallery-link" data-lightbox="gallery">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <div class="gallery-item reveal-item" data-delay="300">
                    <img src="./images/sunday-school-2.jpg" alt="Church Gallery">
                    <div class="gallery-overlay">
                        <a href="../images/sunday-school-2.jpg" class="gallery-link" data-lightbox="gallery">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- <div class="text-center mt-50 reveal-bottom">
                <a href="#" class="btn btn-outline">View Full Gallery</a>
            </div> -->
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact section-padding">
        <div class="container">
            <div class="row">
                <div class="contact-info reveal-left">
                    <h6>GET IN TOUCH</h6>
                    <h2>Contact Us</h2>
                    <p class="lead">
                        We'd love to hear from you! Reach out with any questions about our church, services, or how to
                        get involved.
                    </p>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h5>Address</h5>
                            <p>Ayensudo-Abeyee, Central Region, Ghana, West Africa</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h5>Phone</h5>
                            <p>+233 543 957 330</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h5>Email</h5>
                            <p>faithbaptistchurch@gmail.com</p>
                        </div>
                    </div>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="contact-form reveal-right">
                    <div class="form-card">
                        <form id="contactForm">
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" placeholder="Your name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" placeholder="Your email" required>
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" placeholder="Subject" required>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" rows="5" placeholder="Your message" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3023.2375441350157!2d-74.0059413!3d40.7127837!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQyJzQ2LjAiTiA3NMKwMDAnMjEuNCJX!5e0!3m2!1sen!2sus!4v1635181410000!5m2!1sen!2sus"
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-about">
                    <h4>Faith Baptist Church</h4>
                    <p>A place of worship, community, and spiritual growth where everyone is welcome.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/543957330"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://www.youtube.com"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#events">Events</a></li>
                        <li><a href="#sermons">Sermons</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-services">
                    <h5>Service Times</h5>
                    <ul>
                        <li><a href="#services">Sunday: 8:00 AM - 11:00 AM</a></li>
                        <li><a href="#services">Wednesday: 7:00 PM - 9:00 PM</a></li>
                        <li><a href="#services">Friday: 7:00 PM - 9:00 PM</a></li>
                    </ul>
                </div>
                <div class="footer-newsletter">
                    <h5>Newsletter</h5>
                    <p>Subscribe to our newsletter for updates and announcements.</p>
                    <form class="newsletter-form">
                        <div class="form-group">
                            <input type="email" placeholder="Your email">
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="copyright">
                    <p>&copy; 2025 Faith Baptist Church. All rights reserved.</p>
                </div>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <span class="close-lightbox">&times;</span>
        <img class="lightbox-content" id="lightbox-img">
    </div>

    <!-- Audio Player Modal -->
    <div id="audioPlayerModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background-color: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
            <h3 id="audioTitle" style="margin-bottom: 20px; text-align: center;"></h3>
            <audio id="audioPlayer" controls style="width: 100%; margin-bottom: 20px;"></audio>
            <button id="closeAudioModal" class="btn btn-primary" style="display: block; margin: 0 auto;">Close</button>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="shareModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background-color: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
            <h3 style="margin-bottom: 20px; text-align: center;">Share Sermon</h3>
            <div style="display: flex; justify-content: space-around; margin-bottom: 20px;">
                <a href="#" id="shareFacebook" class="social-share-btn"
                    style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #3b5998;">
                    <i class="fab fa-facebook-f" style="font-size: 24px; margin-bottom: 5px;"></i>
                    <span>Facebook</span>
                </a>
                <a href="#" id="shareTwitter" class="social-share-btn"
                    style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #1da1f2;">
                    <i class="fab fa-twitter" style="font-size: 24px; margin-bottom: 5px;"></i>
                    <span>Twitter</span>
                </a>
                <a href="#" id="shareWhatsapp" class="social-share-btn"
                    style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #25d366;">
                    <i class="fab fa-whatsapp" style="font-size: 24px; margin-bottom: 5px;"></i>
                    <span>WhatsApp</span>
                </a>
                <a href="#" id="shareEmail" class="social-share-btn"
                    style="display: flex; flex-direction: column; align-items: center; text-decoration: none; color: #ea4335;">
                    <i class="fas fa-envelope" style="font-size: 24px; margin-bottom: 5px;"></i>
                    <span>Email</span>
                </a>
            </div>
            <button id="closeShareModal" class="btn btn-primary" style="display: block; margin: 0 auto;">Close</button>
        </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div id="pdfViewerModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center;">
        <div
            style="background-color: white; padding: 20px; border-radius: 10px; width: 90%; height: 90%; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 id="pdfTitle"></h3>
                <button id="closePdfModal"
                    style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <iframe id="pdfViewer" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Sermon functionality
        document.addEventListener("DOMContentLoaded", function () {
            // Audio player modal
            const audioPlayerModal = document.getElementById('audioPlayerModal');
            const audioPlayer = document.getElementById('audioPlayer');
            const audioTitle = document.getElementById('audioTitle');
            const closeAudioModal = document.getElementById('closeAudioModal');

            // Share modal
            const shareModal = document.getElementById('shareModal');
            const shareFacebook = document.getElementById('shareFacebook');
            const shareTwitter = document.getElementById('shareTwitter');
            const shareWhatsapp = document.getElementById('shareWhatsapp');
            const shareEmail = document.getElementById('shareEmail');
            const closeShareModal = document.getElementById('closeShareModal');

            // PDF viewer modal
            const pdfViewerModal = document.getElementById('pdfViewerModal');
            const pdfViewer = document.getElementById('pdfViewer');
            const pdfTitle = document.getElementById('pdfTitle');
            const closePdfModal = document.getElementById('closePdfModal');

            // Play sermon audio
            const sermonPlayButtons = document.querySelectorAll('.sermon-play, .sermon-listen');
            sermonPlayButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const audioSrc = this.getAttribute('data-audio');
                    const title = this.getAttribute('data-title');

                    audioPlayer.src = audioSrc;
                    audioTitle.textContent = title;
                    audioPlayerModal.style.display = 'flex';
                    audioPlayer.play();
                });
            });

            // Close audio modal
            closeAudioModal.addEventListener('click', function () {
                audioPlayer.pause();
                audioPlayerModal.style.display = 'none';
            });

            // Share sermon
            const sermonShareButtons = document.querySelectorAll('.sermon-share');
            sermonShareButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const title = this.getAttribute('data-title');
                    const url = window.location.origin + '/' + this.getAttribute('data-url');

                    // Set up share links
                    shareFacebook.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    shareTwitter.href = `https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}`;
                    shareWhatsapp.href = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
                    shareEmail.href = `mailto:?subject=${encodeURIComponent(title)}&body=${encodeURIComponent('Check out this sermon: ' + url)}`;

                    shareModal.style.display = 'flex';
                });
            });

            // Close share modal
            closeShareModal.addEventListener('click', function () {
                shareModal.style.display = 'none';
            });

            // View PDF
            const sermonReadButtons = document.querySelectorAll('.sermon-read');
            sermonReadButtons.forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const pdfSrc = this.getAttribute('data-pdf');
                    const title = this.getAttribute('data-title');

                    pdfViewer.src = pdfSrc;
                    pdfTitle.textContent = title;
                    pdfViewerModal.style.display = 'flex';
                });
            });

            // Close PDF modal
            closePdfModal.addEventListener('click', function () {
                pdfViewerModal.style.display = 'none';
            });

            // Close modals when clicking outside
            window.addEventListener('click', function (e) {
                if (e.target === audioPlayerModal) {
                    audioPlayer.pause();
                    audioPlayerModal.style.display = 'none';
                }
                if (e.target === shareModal) {
                    shareModal.style.display = 'none';
                }
                if (e.target === pdfViewerModal) {
                    pdfViewerModal.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
<?php $conn -> close(); ?>
