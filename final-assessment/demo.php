<?php
require_once "includes/header.php";
require_once "auth.php";
require_once "classes/user.php";

$students = [
    ["name" => "Rahul", "age" => 22],
    ["name" => "Priya", "age" => 23]
];

$numbers = [1, 2, 2, 3, 4, 4, 5];
$uniqueNumbers = array_unique($numbers);

$first = [1, 2, 3];
$second = [4, 5, 6];

$originalText = "I am learning PHP";
$replacedText = str_replace("PHP", "MySQL", $originalText);

$person = new Person("Mohini", "mohini@example.com");
$userObject = new User("Ayesha", "ayesha@example.com");

$insertExample = '$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");';
?>

<div class="mb-4">
    <span class="badge text-bg-primary mb-2">ASSESSMENT ANSWERS</span>
    <h1 class="fw-bold">PHP & MySQL — Set 2</h1>
    <p class="text-secondary">All 16 questions with short examples.</p>
</div>

<div class="row g-3">

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>1. == vs ===</h5>
                <p class="mb-0"><code>5 == "5"</code> is true; <code>5 === "5"</code> is false because strict comparison
                    also checks type.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>2. Multidimensional array</h5>
                <pre class="mb-0">
                    <code>$students = [
                        ["name" => "Rahul", "age" => 22],
                        ["name" => "Priya", "age" => 23]
                        ];
                    </code>
                </pre>
                <p class="mt-2 mb-0">First student: <?= htmlspecialchars($students[0]["name"]) ?>; second student's age:
                    <?= (int) $students[1]["age"] ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>3. include() vs require()</h5>
                <p class="mb-0"><code>include()</code> warns if a file is missing and continues. <code>require()</code>
                    stops the script with a fatal error. Use <code>require_once</code> to avoid loading a file twice.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>4. Remove duplicates</h5>
                <pre><code>$unique = array_unique([1,2,2,3,4,4,5]);</code></pre>
                Result: <?= htmlspecialchars(implode(", ", $uniqueNumbers)) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>5. Insert a record using PDO</h5>
                <pre class="mb-0">
                    <code><?= htmlspecialchars($insertExample) ?>
                            $stmt->execute([
                            ":name" => $name,
                            ":email" => $email
                            ]);
                    </code>
                </pre>
                <p class="mt-2 mb-0">The Add User form sends its values to <code>actions/add_user.php</code>.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>6. Find names starting with A</h5>
                <pre class="mb-0">
                    <code>SELECT * FROM users WHERE name LIKE 'A%' ORDER BY id DESC;</code>
                </pre>
                <p class="mt-2 mb-0">Try this on the <a href="users.php?search_a=1">All Users page</a>.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>7. Public, private, protected</h5>
                <p class="mb-0"><code>Person</code> has a public name, private email, and protected role. The private
                    email is accessed with a public getter: <?= htmlspecialchars($person->getEmail()) ?>.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>8. Inheritance and overriding</h5>
                <p class="mb-0"><code>User</code> extends <code>Person</code> and overrides <code>showDetails()</code>.
                </p>
                <pre class="mt-2 mb-0"><code><?= htmlspecialchars($userObject->showDetails()) ?></code></pre>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>9. var, let, const</h5>
                <p class="mb-0"><code>var</code> is function-scoped; <code>let</code> is block-scoped and can be
                    reassigned; <code>const</code> is block-scoped and cannot be reassigned.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>10. Validate email with JavaScript regex</h5>
                <pre class="mb-0">
                    <code>const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        pattern.test(email);
                    </code>
                </pre>
                <p class="mt-2 mb-0">Used by the Add User form.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>11. Change an element's text</h5>
                <pre class="mb-0">
                    <code>$(".title").text("PHP Practical Project");</code>
                </pre>
                <p class="mt-2 mb-0">Click “Change title” below to see the jQuery change.</p>
                <h6 class="title mt-3">Original project title</h6>
                <button id="changeTitle" class="btn btn-outline-primary btn-sm">Change title</button>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>12. Submit a form with jQuery</h5>
                <pre class="mb-0">
                    <code>$("#userForm").on("submit", function (event) {
                            event.preventDefault();
                    });</code>
                </pre>
                <p class="mt-2 mb-0">The Add User form handles submit with jQuery.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>13. AJAX POST request</h5>
                <pre class="mb-0">
                    <code>$.ajax({
                    url: "actions/add_user.php",
                    type: "POST",
                    data: $("#userForm").serialize()
                    });
                    </code>
                </pre>
                <p class="mt-2 mb-0">The Add User form sends this request.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>14. Merge two arrays</h5>
                <pre><code>$merged = array_merge([1,2,3], [4,5,6]);</code></pre>
                Result: <?= htmlspecialchars(implode(", ", array_merge($first, $second))) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>15. Set and get a cookie</h5>
                <p class="mb-0">Open the <a href="cookie.php">Cookie Demo</a> page to set a cookie and read its value.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card app-card shadow-sm h-100">
            <div class="card-body">
                <h5>16. Replace a word</h5>
                <pre class="mb-0"><code>$newText = str_replace("PHP", "MySQL", $text);</code></pre>
                <p class="mt-2 mb-0"><?= htmlspecialchars($replacedText) ?></p>
            </div>
        </div>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>