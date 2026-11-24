<?php

namespace App\Exceptions;

use RuntimeException;

/** Kesalahan saat berkomunikasi dengan Midtrans (pesannya aman ditampilkan ke pengguna). */
class MidtransException extends RuntimeException
{
}
