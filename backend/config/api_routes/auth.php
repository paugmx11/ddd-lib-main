<?php

declare(strict_types=1);

return [
    ['POST', '#^/api/auth/register$#', 'authApi.register', true],
    ['POST', '#^/api/auth/login$#', 'authApi.login', true],
    ['POST', '#^/api/auth/logout$#', 'authApi.logout'],
    ['POST', '#^/api/auth/google/exchange$#', 'authApi.exchangeGoogleCode', true],
];
