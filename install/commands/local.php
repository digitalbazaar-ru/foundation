<?php

namespace App\Console\Command;

use Foundation\Disposer;

Disposer::add(new Local\TestCommand());