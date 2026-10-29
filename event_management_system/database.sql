CREATE DATABASE IF NOT EXISTS event_management_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_management_db;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    participant_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(25) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_registration_event
      FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_email (event_id, email)
);

INSERT INTO events (title, description, event_date, event_time, venue) VALUES
('Annual Cultural Fest', 'A day of music, dance, performances and student activities.', '2027-02-15', '10:00:00', 'College Auditorium'),
('Web Development Workshop', 'A beginner-friendly workshop on HTML, CSS, PHP and MySQL.', '2027-03-05', '11:00:00', 'Computer Lab 2'),
('Inter-College Sports Meet', 'A sports meet featuring indoor and outdoor games.', '2027-03-20', '09:00:00', 'College Sports Ground');
