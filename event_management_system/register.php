<?php
require_once 'db.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$eventId = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
}

if (!$eventId || $eventId < 1) {
    header('Location: index.php?error=' . urlencode('Please select a valid event.'));
    exit;
}

$stmt = $conn->prepare("SELECT id, title, event_date, event_time, venue FROM events WHERE id = ?");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
if (!$event) {
    header('Location: index.php?error=' . urlencode('Event not found.'));
    exit;
}

$errors = [];
$name = $email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['participant_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || mb_strlen($name) > 100) $errors[] = 'Enter a name (maximum 100 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) $errors[] = 'Enter a valid email address.';
    if ($phone === '' || !preg_match('/^[0-9+\-\s()]{7,25}$/', $phone)) $errors[] = 'Enter a valid phone number (7–25 characters).';

    if (!$errors) {
        $insert = $conn->prepare("INSERT INTO registrations (event_id, participant_name, email, phone) VALUES (?, ?, ?, ?)");
        $insert->bind_param("isss", $eventId, $name, $email, $phone);
        if ($insert->execute()) {
            header('Location: index.php?registered=1');
            exit;
        }
        if ($conn->errno === 1062 || $insert->errno === 1062) {
            $errors[] = 'This email is already registered for this event.';
        } else {
            $errors[] = 'Registration could not be saved. Please try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register | Campus Events</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar"><a class="brand" href="index.php">Campus<span>Events</span></a><nav><a href="index.php">Events</a><a href="participants.php">Participant Lists</a></nav></header>
<main class="container narrow">
  <a class="back-link" href="index.php">← Back to events</a>
  <section class="form-panel">
    <p class="eyebrow">SAVE YOUR SPOT</p>
    <h1>Event registration</h1>
    <div class="event-summary">
      <h3><?= h($event['title']) ?></h3>
      <p><?= h(date('d M Y', strtotime($event['event_date']))) ?> · <?= h(date('g:i A', strtotime($event['event_time']))) ?></p>
      <p><?= h($event['venue']) ?></p>
    </div>
    <?php foreach ($errors as $error): ?><div class="notice warning"><?= h($error) ?></div><?php endforeach; ?>
    <form method="post" action="register.php">
      <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
      <label for="participant_name">Full name</label>
      <input id="participant_name" name="participant_name" maxlength="100" value="<?= h($name) ?>" required>
      <label for="email">Email address</label>
      <input id="email" type="email" name="email" maxlength="150" value="<?= h($email) ?>" required>
      <label for="phone">Phone number</label>
      <input id="phone" type="tel" name="phone" maxlength="25" value="<?= h($phone) ?>" required>
      <button class="button full" type="submit">Confirm registration →</button>
    </form>
  </section>
  <footer>Only register with contact details you are comfortable sharing with the event organiser.</footer>
</main>
</body>
</html>
