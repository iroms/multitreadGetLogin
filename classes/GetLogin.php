<?php

    class GetLogin
    {

        public $name;
        public $surname;

        public function __construct($name, $surname)
        {
            $this->name    = $name;
            $this->surname = $surname;
        }

        public function translit($text)
        {

            $translitRule = array(
                'а' => "a",
                'б' => "b",
                'в' => "v",
                'г' => "g",
                'д' => "d",
                'е' => "e",
                'ё' => "e",
                'ж' => "zh",
                'з' => "z",
                'и' => "i",
                'й' => "j",
                'к' => "k",
                'л' => "l",
                'м' => "m",
                'н' => "n",
                'о' => "o",
                'п' => "p",
                'р' => "r",
                'с' => "s",
                'т' => "t",
                'у' => "u",
                'ф' => "f",
                'х' => "h",
                'ц' => "tc",
                'ч' => "ch",
                'ш' => "sh",
                'щ' => "shh",
                'ъ' => "",
                'ы' => "y",
                'ь' => "",
                'э' => "e",
                'ю' => "yu",
                'я' => "ya",
            );

            $text = mb_strtolower($text);
            $text = strtr($text, $translitRule);
            $text = preg_replace('/[^a-z]/', '', $text);
            return $text;

        }

        public function getLogin($loginMaxLength = 20)
        {

            $loginName    = $this->translit($this->name);
            $loginSurname = $this->translit($this->surname);

            if (!$loginName and !$loginSurname)     // no name and surname or no cirillic symbols
            {
                $login = '';
            }
            elseif ($loginName and $loginSurname)   // good name and good surname
            {
                $login = $loginName . '.' . $loginSurname;

                if (strlen($login) > $loginMaxLength)
                {
                    $login = $loginSurname;

                    if (strlen($login) > $loginMaxLength)
                    {
                        $login = substr($login, 0, $loginMaxLength);
                    }
                }
            }
            else                                    // good only name or good only surname
            {
                if ($loginName) 
                {
                    $login = $loginName;
                }
                else
                {
                    $login = $loginSurname;
                }

                if (strlen($login) > $loginMaxLength)
                {
                    $login = $loginSurname;

                    if (strlen($login) > $loginMaxLength)
                    {
                        $login = substr($login, 0, $loginMaxLength);
                    }
                }
            }

            return $login;

        }

    }


