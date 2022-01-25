<?php
session_start();

require_once 'config.php';
require_once 'vendor/autoload.php';

use Medoo\Medoo;

class Review
{
    private $db;
    private $error = [];

    public function __construct()
    {
        $this->db = new Medoo([
            'type' => DB_TYPE,
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASS
        ]);
    }

    public function index()
    {
        $data = [];

        if (!empty($_POST) && $this->validate($_POST)) {
            $this->db->insert('reviews', [
                'author' => strip_tags(trim($_POST['author'])),
                'text' => strip_tags(trim($_POST['text'])),
                'date_added' => Medoo::raw('NOW()')
            ]);

            $_SESSION['success'] = 'Отзыв успешно отправлен!';
        }

        if (isset($_SESSION['success'])) {
            $data['success'] = $_SESSION['success'];

            unset($_SESSION['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['author'])) {
            $data['error_author'] = $this->error['author'];
        } else {
            $data['error_author'] = '';
        }

        if (isset($this->error['text'])) {
            $data['error_text'] = $this->error['text'];
        } else {
            $data['error_text'] = '';
        }

        $review_data = $this->db->select('reviews', '*', [
            'ORDER' => 'date_added'
        ]);

        $data['reviews'] = $this->prepareReviews($review_data);

        return $data;
    }

    protected function prepareReviews(array $data)
    {
        $formated = [];

        foreach ($data as $item) {
            $formated[] = array(
                'author' => $item['author'],
                'text' => $item['text'],
                'date_added' => date('H:i d.m.Y', strtotime($item['date_added']))
            );
        }

        return $formated;
    }

    protected function validate()
    {
        if (empty(strip_tags(trim($_POST['author'])))) {
            $this->error['author'] = 'Введите автора!';
        }

        if (empty(strip_tags(trim($_POST['text'])))) {
            $this->error['text'] = 'Введите комментарий!';
        }

        return !$this->error;
    }
}

$review = new Review();
$data = $review->index();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отзывы</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="row align-items-md-stretch">
                <div class="col-12 col-md-10 order-2 order-md-1">
                    <div class="d-flex flex-column justify-content-md-between h-100 text-center text-md-start">
                        <div class="contacts mt-md-3 mb-3 mb-md-0">
                            <div class="contact-item mb-2">Телефон: <a href="tel:(499) 340-94-71">(499) 340-94-71</a></div>
                            <div class="contact-item">Email: <a href="info@future-group.ru">info@future-group.ru</a></div>
                        </div>
                        <h1 class="page-title">Комментарии</h1>
                    </div>
                </div>
                <div class="col-12 col-md-2 order-1 order-md-2 mb-3 mb-md-0">
                    <img src="assets/image/logo.png" alt="" class="img-fluid d-block mx-auto mx-md-0">
                </div>
            </div>
        </div>
    </header>
    <section>
        <div class="container">
            <?php if ($data['reviews']) { ?>
                <div class="reviews">
                    <?php foreach ($data['reviews'] as $review) { ?>
                        <div class="review-item">
                            <div class="review-item-head d-flex align-items-center">
                                <span class="review-item-author me-4"><?php echo $review['author']; ?></span>
                                <span class="review-item-date"><?php echo $review['date_added']; ?></span>
                            </div>
                            <div class="review-item-text">
                                <?php echo $review['text']; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>Комментарии отсутствуют</p>
            <?php } ?>
            <hr>
            <div class="row">
                <div class="col-12 col-md-6">
                    <form action="/" method="post" enctype="multipart/form-data" class="review-form">
                        <fieldset>
                            <legend class="mb-3">Оставить комментарий</legend>
                            <?php if ($data['success']) { ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <?php echo $data['success']; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php } ?>
                            <div class="mb-3">
                                <label for="input-name">Ваше имя</label>
                                <input type="text" name="author" id="input-name" placeholder="Ваше имя" />
                                <?php if ($data['error_author']) { ?>
                                    <div class="text-danger"><?php echo $data['error_author']; ?></div>
                                <?php } ?>
                            </div>
                            <div class="mb-2">
                                <label for="input-text">Ваш комментарий</label>
                                <textarea id="input-text" name="text" placeholder="Ваш комментарий" rows="5"></textarea>
                                <?php if ($data['error_text']) { ?>
                                    <div class="text-danger"><?php echo $data['error_text']; ?></div>
                                <?php } ?>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit">Отправить</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>

        </div>
    </section>
    <footer>
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-md-end">
                <div class="footer-logo mb-3 mb-md-0 me-md-5">
                    <img src="assets/image/footer_logo.png" alt="" class="img-fluid d-block mx-auto mx-md-0">
                </div>
                <div class="footer-contacts text-center text-md-start mt-md-3">
                    <div class="contact-item mb-1">Телефон: <a href="tel:(499) 340-94-71">(499) 340-94-71</a></div>
                    <div class="contact-item mb-1">Email: <a href="mailto:info@future-group.ru">info@future-group.ru</a></div>
                    <div class="contact-item mb-3">Адрес: <a href="https://www.google.com.ua/">115088 Москва, ул. 2-я Машиностроения, д. 7 стр. 1</a></div>

                    <div class="powered">© 2010 - 2014 Future. Все права защищены</div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>