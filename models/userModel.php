<?php
class UserModel
{
    // methode pour recuperer la liste des utilisateurs 
    public static function getUserList()
    {
        // on se connect a la base de donnes
        $db = self::dbConnect();
        // preparer la requete 
        $request = $db->prepare("SELECT* FROM user");
        try {
            $request->execute();
            $listUser = $request->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                "status" => 200,
                "message" => "your message is safely sent...",
                "users" => $listUser
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }

    // fonction pour se connecter a la bd
    private static function dbConnect()
    {
        $conn = null;
        try {
            $conn = new PDO("mysql:host=localhost;dbname=api_db", "root", "");
        } catch (PDOException $e) {
            $conn = $e->getMessage();
        }
        return $conn;
    }

    public static function getListMessage($expeditor, $receiver)
    {
        // se connecter a la base de données
        $db = self::dbConnect();
        // preparer la requete
        $request = $db->prepare("SELECT * FROM messages WHERE expeditor_id = ? AND receiver_id =? OR expeditor_id = ? AND receiver_id = ?");
        // executer la requete
        try {
            $request->execute(array($expeditor, $receiver, $receiver, $expeditor));
            // recuperer le resultat dans un tableau
            $messages = $request->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                "status" => 200,
                "message" => "voici la liste de votre discution",
                "listMessage" => $messages
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }


    // fonction pour se connecter
    public static function login($pseudo, $password)
    {
        // se connecter a la db
        $db = self::dbConnect();
        // preparer la requete
        $request = $db->prepare("SELECT * FROM user WHERE pseudo = ?");
        // executer la requete
        try {
            $request->execute(array($pseudo));
            // recuperer la reponse de la requete
            $user = $request->fetch(PDO::FETCH_ASSOC);
            // on verifie si l'utilisateur existe
            if (empty($user)) {
                echo json_encode([
                    "status" => 401,
                    "message" => "user not found"
                ]);
            } else {
                // verifier si le password est corret
                if (password_verify($password, $user['password'])) {
                    echo json_encode([
                        "status" => 200,
                        "message" => "felicitation...",
                        "userInfo" => $user
                    ]);
                } else {
                    echo json_encode([
                        "status" => 404,
                        "message" =>  "password incorrect"
                    ]);
                }
            }
        } catch (PDOException $e) {
            echo json_encode([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }

    // function pour enregistrer un utilisateur
    public static function register($firstName, $lastName, $pseudo, $password)
    {
        // hasher le mot de passe
        $passwordCrypt = password_hash($password, PASSWORD_DEFAULT);
        // connexion a la db
        $db = self::dbConnect();
        // preparer la requete
        $request = $db->prepare('INSERT INTO user (pseudo, password,firstname, lastname) VALUES (?, ?, ?, ?)');
        try {
            $request->execute(array($pseudo, $passwordCrypt, $firstName, $lastName));
            echo json_encode([
                "status" => 201,
                "message" => "everything",
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }

    public static function sendMessage($expeditor, $receiver, $message)
    {
        // se connecter a la db
        $db = self::dbConnect();
        // preparer la requete
        $request = $db->prepare("INSERT INTO messages (message, expeditor_id, receiver_id) VALUES (?,?,?)");
        // executer la requete
        try {
            $request->execute(array($message, $expeditor, $receiver));
            echo json_encode([
                "status" => 201,
                "message" => "your message is safely sent.."
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }
}
