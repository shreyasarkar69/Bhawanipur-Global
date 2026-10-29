<?php
require_once 'db.php';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$selectedEvent = filter_input(INPUT_GET, 'event_id', FILTER_VALIDATE_INT);
$eventList = $conn->query("SELECT id, title, event_date FROM events ORDER BY event_date, event_time");
$participants = null;
$selected = null;

if ($selectedEvent && $selectedEvent > 0) {
    $eventStmt = $conn->prepare("SELECT id, title, event_date, event_time, venue FROM events WHERE id = ?");
    $eventStmt->bind_param("i", $selectedEvent);
    $eventStmt->execute();
    $selected = $eventStmt->get_result()->fetch_assoc();

    if ($selected) {
        $stmt = $conn->prepare("SELECT participant_name, email, phone, registered_at FROM registrations WHERE event_id = ? ORDER BY participant_name");
        $stmt->bind_param("i", $selectedEvent);
        $stmt->execute();
        $participants = $stmt->get_result();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Participant Lists | Campus Events</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar"><a class="brand" href="index.php">Campus<span>Events</span></a><nav><a href="index.php">Events</a><a class="active" href="participants.php">Participant Lists</a></nav></header>
<main class="container">
  <section class="hero compact"><p class="eyebrow">EVENT DIRECTORY</p><h1>Participant <span>lists.</span></h1><p>Choose an event to see its registered participants.</p></section>
  <section class="form-panel">
    <form method="get" action="participants.php">
      <label for="event_id">Select event</label>
      <select id="event_id" name="event_id" required>
        <option value="">Choose an event</option>
        <?php if ($eventList): while ($e = $eventList->fetch_assoc()): ?>
          <option value="<?= (int)$e['id'] ?>" <?= $selectedEvent === (int)$e['id'] ? 'selected' : '' ?>><?= h($e['title']) ?> — <?= h(date('d M Y', strtotime($e['event_date']))) ?></option>
        <?php endwhile; endif; ?>
      </select>
      <button class="button" type="submit">View participants</button>
    </form>
  </section>

  <?php if ($selected): ?>
    <div class="section-heading"><div><p class="eyebrow">REGISTERED ATTENDEES</p><h2><?= h($selected['title']) ?></h2><p class="meta"><?= h($selected['venue']) ?> · <?= h(date('d M Y', strtotime($selected['event_date']))) ?></p></div><span class="count"><?= $participants->num_rows ?> participants</span></div>
    <div class="table-wrap"><table><thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th></tr></thead><tbody>
    <?php if ($participants->num_rows): $n = 1; while ($p = $participants->fetch_assoc()): ?>
      <tr><td><?= $n++ ?></td><td><?= h($p['participant_name']) ?></td><td><?= h($p['email']) ?></td><td><?= h($p['phone']) ?></td></tr>
    <?php endwhile; else: ?>
      <tr><td colspan="4">No participants have registered for this event yet.</td></tr>
    <?php endif; ?>
    </tbody></table></div>
  <?php elseif ($selectedEvent): ?>
    <div class="notice warning">That event could not be found.</div>
  <?php endif; ?>
  <footer>Participant details are shown for this academic demonstration project.</footer>
</main>
</body>
</html>
