<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Blacklist Mode
    |--------------------------------------------------------------------------
    |
    | This setting determines which word lists to use when validating user input.
    | Options:
    | - 'blacklist': Only use the system blacklist words
    | - 'profanity': Only use the profanity/offensive words
    | - 'both': Use both blacklist and profanity words
    |
    */

    'mode' => 'profanity', // Options: 'blacklist', 'profanity', 'both'

    'default_matching' => 'substitution', // Options: 'exact', 'fuzzy', 'substitution'

    /*
    |--------------------------------------------------------------------------
    | System Blacklist
    |--------------------------------------------------------------------------
    |
    | This array contains system-related words that should be blacklisted when validating
    | user input. Any field containing these words will be rejected.
    | These are typically used to prevent username squatting or system impersonation.
    |
    */
    'blacklist' => [
        // System roles and positions
        'system',
        'god',
        'super',
        'admin',
        'admins',
        'administrator',
        'administrators',
        'moderator',
        'mod',
        'superuser',
        'supervisor',
        'sysadmin',
        'webmaster',
        'webadmin',
        'root',
        'owner',
        'master',
        'manager',

        // Corporate roles
        'ceo',
        'cfo',
        'coo',
        'cto',
        'president',
        'vp',
        'executive',
        'director',
        'chairman',
        'founder',

        // Department names
        'marketing',
        'sales',
        'support',
        'helpdesk',
        'customer',
        'service',
        'billing',
        'finance',
        'legal',
        'hr',
        'security',
        'tech',
        'it',
        'engineering',
        'development',

        // System terms
        'abuse',
        'account',
        'adm',
        'all',
        'contact',
        'document',
        'download',
        'faq',
        'file',
        'files',
        'ftp',
        'help',
        'home',
        'host',
        'http',
        'https',
        'imap',
        'info',
        'ldap',
        'list',
        'majordomo',
        'member',
        'membership',
        'mis',
        'news',
        'noreply',
        'no-reply',
        'donotreply',
        'do-not-reply',
        'office',
        'password',
        'pop',
        'postfix',
        'postmaster',
        'register',
        'registration',
        'secure',
        'security',
        'sftp',
        'shop',
        'smtp',
        'ssl',
        'test',
        'trouble',
        'usenet',
        'user',
        'web',
        'webserver',
        'wheel',
        'vww',
        'wvw',
        'wwv',
        'www',
        'www-data',

        // Brand protection
        'official',
        'staff',
        'team',
        'support',
        'help',
        'service',
        'verified',
        'real',
        'genuine',
        'original',
        'authentic',

        // Common platform names (add your app name here)
        'facebook',
        'instagram',
        'twitter',
        'tiktok',
        'youtube',
        'linkedin',
        'pinterest',
        'snapchat',
        'reddit',
        'discord',
        'slack',
        'github',
        'gitlab',
        'bitbucket',
        'paypal',
        'stripe',
        'amazon',
        'google',
        'microsoft',
        'apple',
    ],

    /*
    |--------------------------------------------------------------------------
    | Profanity List
    |--------------------------------------------------------------------------
    |
    | This array contains profanity, curse words, and offensive terms that should
    | be blacklisted when validating user input. Any field containing these words
    | will be rejected.
    |
    */
    'profanity' => [

    /*
    |--------------------------------------------------------------------------
    | ENGLISH PROFANITY
    |--------------------------------------------------------------------------
    */

    'ass',
    'asses',
    'asshole',
    'assholes',
    'asshat',
    'asshats',
    'asswipe',
    'asswipes',
    'assclown',
    'assclowns',
    'assface',
    'assfaces',

    'bastard',
    'bastards',

    'bitch',
    'bitches',
    'bitching',
    'bitchy',

    'bullshit',
    'bullshits',
    'bullshitting',
    'bullshitter',

    'crap',
    'crappy',

    'damn',
    'damned',
    'dammit',

    'dick',
    'dicks',
    'dickhead',
    'dickheads',
    'dickweed',
    'dickwad',

    'douche',
    'douches',
    'douchebag',
    'douchebags',

    'fuck',
    'fucks',
    'fucked',
    'fucker',
    'fuckers',
    'fucking',
    'fuckface',
    'fuckfaces',
    'fuckhead',
    'fuckheads',
    'fuckwit',
    'fuckwits',
    'fuckwad',
    'fuckwads',
    'fuckup',
    'fuckups',

    'motherfuck',
    'motherfucker',
    'motherfuckers',
    'motherfucking',

    'shit',
    'shits',
    'shitty',
    'shitting',
    'shithead',
    'shitheads',
    'shitface',
    'shitfaces',
    'shitbag',
    'shitbags',
    'shitshow',

    'dipshit',
    'dipshits',
    'dumbshit',
    'dumbshits',
    'dumbfuck',
    'dumbfucks',
    'dumbass',
    'dumbasses',

    'piss',
    'pissed',
    'pissing',

    'prick',
    'pricks',

    'twat',
    'twats',

    'wanker',
    'wankers',

    'whore',
    'whores',

    'slut',
    'sluts',
    'slutty',

    'skank',
    'skanks',

    'jackass',
    'jackasses',

    'scumbag',
    'scumbags',

    'bollocks',

    /*
    |--------------------------------------------------------------------------
    | ENGLISH SEXUAL / EXPLICIT
    |--------------------------------------------------------------------------
    */

    'cock',
    'cocks',
    'cocksucker',
    'cocksuckers',

    'cunt',
    'cunts',

    'pussy',
    'pussies',

    'cum',
    'cums',
    'cumming',
    'cumshot',
    'cumshots',
    'cumslut',
    'cumsluts',

    'jizz',
    'jizzing',

    'blowjob',
    'blowjobs',

    'handjob',
    'handjobs',

    'masturbate',
    'masturbating',
    'masturbation',

    'orgasm',
    'orgasms',

    'ejaculate',
    'ejaculation',
    'ejaculating',

    'penis',
    'penises',
    'vagina',
    'vaginas',

    'genital',
    'genitals',

    'boob',
    'boobs',
    'tit',
    'tits',
    'titty',
    'titties',
    'nipple',
    'nipples',

    /*
    |--------------------------------------------------------------------------
    | ENGLISH SLURS
    |--------------------------------------------------------------------------
    */

    'nigger',
    'nigga',
    'faggot',
    'fag',
    'dyke',
    'kike',
    'spic',
    'chink',
    'gook',
    'wetback',
    'paki',
    'raghead',
    'towelhead',
    'tranny',

    'retard',
    'retarded',
    'tard',
    'spaz',
    'spastic',

    /*
    |--------------------------------------------------------------------------
    | ENGLISH INSULTS
    |--------------------------------------------------------------------------
    */

    'idiot',
    'idiots',

    'imbecile',
    'imbeciles',

    'moron',
    'morons',

    'stupid',
    'stupidity',

    'loser',
    'losers',

    'pathetic',
    'worthless',

    'failure',
    'failures',

    'cretin',
    'cretins',

    /*
    |--------------------------------------------------------------------------
    | ENGLISH INTERNET PROFANITY
    |--------------------------------------------------------------------------
    */

    'wtf',
    'wtfh',
    'wtaf',

    'stfu',
    'gtfo',
    'ffs',

    'omfg',
    'lmfao',
    'lmao',

    'af',
    'bs',

    /*
    |--------------------------------------------------------------------------
    | MILD ENGLISH PROFANITY
    |--------------------------------------------------------------------------
    */

    'hell',
    'heck',
    'darn',

    'suck',
    'sucks',
    'sucked',
    'sucker',

    /*
    |--------------------------------------------------------------------------
    | EUPHEMISMS
    |--------------------------------------------------------------------------
    */

    'frick',
    'fricking',
    'freaking',
    'effing',
    'wth',
    'omg',


    /*
    |--------------------------------------------------------------------------
    | TAGALOG / FILIPINO
    |--------------------------------------------------------------------------
    */

    'puta',
    'putsa',
    'putang',
    'putangina',
    'putang ina',
    'putanginamo',
    'putang ina mo',
    'putangina mo',
    'putangina nyo',
    'putang ina nyo',

    'tangina',
    'tang ina',
    'tangina mo',
    'tang ina mo',

    'taena',
    'ta e na',

    'kingina',
    'king ina',

    'amputa',
    'ampota',

    'anak ng puta',
    'anak ng putsa',
    'anak ka ng puta',
    'anak ka nang puta',

    'pucha',
    'puchang',
    'puchangina',

    'pakyu',
    'pakyu ka',
    'pakyo',
    'pakyet',

    'pakshet',
    'pakshit',
    'packshit',

    'punyeta',
    'punyeto',
    'punyetang',
    'punyeta ka',
    'punyeta kayo',

    'leche',
    'letse',
    'letse ka',
    'leche ka',

    'lintik',
    'lintek',
    'lintik ka',
    'lintek ka',

    'bwisit',
    'buwisit',
    'bwesit',
    'bwiset',
    'buset',
    'buwesit',
    'bwisit ka',
    'buwisit ka',

    'gago',
    'gaga',
    'gagi',
    'gagong',
    'gago ka',
    'gaga ka',

    'tanga',
    'tangahan',
    'tanga ka',

    'bobo',
    'bobo ka',
    'bobong',

    'ulol',
    'ulol ka',

    'tarantado',
    'tarantada',
    'tarantadong',
    'tarantado ka',

    'kupal',
    'kupalan',
    'kupal ka',

    'inutil',
    'inutil ka',

    'ogag',
    'ogags',

    'ungas',

    'gunggong',
    'gunggong ka',

    'siraulo',
    'siraulo ka',

    'hudas',

    'demonyo',
    'demonyo ka',

    'peste',
    'peste ka',
    'pesteng',

    'hayop',
    'hayop ka',
    'animal',
    'animal ka',

    'hinayupak',
    'hinayupak ka',

    'walanghiya',
    'walang hiya',
    'walang hiya ka',

    'wala kang kwenta',
    'walang kwenta',
    'walang kwentang tao',
    'walang kwentang buhay',

    /*
    |--------------------------------------------------------------------------
    | TAGALOG BODY / SEXUAL PROFANITY
    |--------------------------------------------------------------------------
    */

    'burat',
    'burat mo',

    'bayag',
    'bayagan',

    'titi',
    'tite',

    'puke',
    'puki',

    'bilat',

    'pekpek',
    'pepek',

    'supot',

    'kantot',
    'kantutan',
    'kinantot',
    'makipagkantutan',

    'iyot',
    'umiiyot',
    'nakikipagiyot',

    'jakol',
    'nagjajakol',

    /*
    |--------------------------------------------------------------------------
    | TAGALOG EXCREMENT / GROSS PROFANITY
    |--------------------------------------------------------------------------
    */

    'tae',
    'tae mo',
    'tae ka',

    'tatae',
    'tumae',

    'kainin mo tae ko',

    /*
    |--------------------------------------------------------------------------
    | TAGALOG PHRASES
    |--------------------------------------------------------------------------
    */

    'puke ng ina mo',
    'puki ng ina mo',
    'puke nang ina mo',
    'puki nang ina mo',

    'putang ina mo',
    'putang ina nyo',

    'walang hiya ka',
    'peste ka',
    'hayop ka',
    'animal ka',

    'pakyu ka',

    'leche ka',
    'lintik ka',
    'lintek ka',

    'inutil ka',
    'gunggong ka',

    'ulol na gago',
    'tarantadong gago',

    'hudas kang hayop ka',

    'pesteng yawa',

    /*
    |--------------------------------------------------------------------------
    | TAGALOG EUPHEMISMS / CENSORED VARIANTS
    |--------------------------------------------------------------------------
    */

    'shet',
    'syet',
    'sh*t',
    'shetang',
    'syetang',

    'p*ta',
    'p*uta',
    'p*tangina',
    'p*tang ina',

    't*ngina',
    't*ng ina',

    'g*go',
    't*nga',
    'b*bo',

    /*
    |--------------------------------------------------------------------------
    | BISAYA / CEBUANO
    |--------------------------------------------------------------------------
    */

    'yawa',
    'yawawa',
    'yawardz',

    'pisti',
    'piste',
    'piste ka',
    'pisti ka',

    'atay',
    'atay ka',

    'buang',
    'boang',
    'buang ka',
    'boang ka',

    'amaw',
    'amaw ka',

    'bogo',
    'bugo',
    'bogo ka',
    'bugo ka',

    'inutil',

    'puta',
    'puta ka',

    'bigaon',

    'iyot',

    'oten',

    'bilat',
    'bilat sa imong ina',

    'buli',

    'baboy',
    'baboy ka',

    'hiwi',

    'pugaw',

    'bastardo',

    'anak sa gawas',

    'irong baye',
    'babayeng ulagan',

    'boysette',
    'boyset',

    'pesteng yawa',

    /*
    |--------------------------------------------------------------------------
    | COMMON FILIPINO SPELLING VARIATIONS
    |--------------------------------------------------------------------------
    */

    'putangina',
    'putang-ina',
    'putang ina',
    'putang-ina mo',

    'tangina',
    'tang-ina',
    'tangina mo',
    'tang-ina mo',

    'pakyu',
    'pak-yu',
    'pakyo',
    'pak-yoh',

    'pakshet',
    'pak-shet',
    'paksit',

    'punyeta',
    'pun-yeta',

    'bwisit',
    'buwisit',
    'bwesit',
    'bwiset',
    'buset',

    'gago',
    'gagi',

    /*
    |--------------------------------------------------------------------------
    | COMMON FILIPINO TEXT / CHAT SHORTENINGS
    |--------------------------------------------------------------------------
    */

    'tngina',
    'tnagina',
    'tangnina',

    'ptangina',
    'ptngina',

    'putangn',
    'putanginaaa',

    'gag0',
    'g4go',

    't4nga',
    't4ngina',

    'b0bo',
    'ul0l',

    'pvtangina',
    'pvtang ina',
    
],

    /*
    |--------------------------------------------------------------------------
    | Whitelist
    |--------------------------------------------------------------------------
    |
    | This array contains whitelisted words that should not be blacklisted when validating
    | user input. These words can still appear in fields but won't trigger a validation error.
    |
    */
    'whitelist' => [
        // 'OpenAI',
        // 'Laravel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignore patterns
    |--------------------------------------------------------------------------
    |
    | This array contains regex patterns for ignoring certain words during validation.
    | If any of these patterns match a word, it will be ignored and not considered as invalid.
    |
    */
    'ignore_patterns' => [
        // '/^foo.*bar$/i',
    ],

    /*
    |--------------------------------------------------------------------------
    | Advance matching strategies
    |--------------------------------------------------------------------------
    |
    | This array contains advanced strategies for matching words against the blacklist.
    | Each strategy has its own set of rules and conditions for determining if a word matches.
    |
    */
    'lists' => [
        'blacklist' => [
            'terms' => ['spam', 'scam'],
            'matching' => 'exact',
        ],

        'profanity' => [
            'terms' => ['badword'],
            'matching' => 'fuzzy',
            'threshold' => 2,
        ],

        'leetspeak' => [
            'terms' => ['shit'],
            'matching' => 'substitution',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Per-Field Rules & Contexts
    |--------------------------------------------------------------------------
    |
    | This array allows you to define custom validation rules for specific fields.
    | You can specify different modes, whitelist entries, ignore patterns, etc., on a per-field basis.
    |
    */
    'contexts' => [
        // 'username' => ['blacklist'],
        // 'comment' => ['blacklist', 'profanity', 'leetspeak'],
        // 'bio' => ['blacklist'],
    ],
];
