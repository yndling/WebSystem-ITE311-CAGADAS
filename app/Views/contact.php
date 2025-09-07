<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Learning Management System - Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">LMS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/') ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= site_url('/about') ?>">About</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?= site_url('/contact') ?>">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1>Contact Us</h1>
        <p class="lead">Get in touch with our team for support or inquiries about the Learning Management System.</p>
        <div class="row">
            <div class="col-md-6">
                <h3>Contact Information</h3>
                <p><strong>Email:</strong> support@lms.com</p>
                <p><strong>Phone:</strong> +1 (123) 456-7890</p>
                <p><strong>Address:</strong> 123 Learning St, Education City, EC 12345</p>
            </div>
            <div class="col-md-6">
                <h3>Send a Message</h3>
                <form>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Your Name">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="your@email.com">
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="4" placeholder="Your message"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
        <div class="text-center my-4">
            <img src="https://via.placeholder.com/600x300?text=Contact+Us" alt="Contact Us" class="img-fluid rounded" />
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
