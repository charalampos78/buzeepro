<?php

return [
    'denyMessage' => 'You are not allowed access to {route}',
    'denyRedirect' => '/',
    'defaultRule' => [
        'deny',
        'redirect' => '/login',
    ],
    'globalRules' => [
        [
            'allow',
            'roles' => ['admin']
        ]
    ],
    'rules' => [
        'uris' => [
            'manage/users/edit' => [
                [
                    'allow',
                ]
            ]
        ],
        'controller' => [
            'AccountController' => [
                [
                    'allow',
                    'actions' => ['login', 'register', 'recover'],
                    'users' => ['?'],
                ],
                [
                    'allow',
                    'actions' => ['edit', 'getJwt'],
                    'users' => ['@'],
                ],
                [
                    'deny',
                    'actions' => ['login', 'register'],
                    'users' => ['@'],
                    'redirect' => '/',
                    'message' => "Sorry, you can't access {route} while you're already logged in",
                ],
                [
                    'allow',
                    'actions' => ['logout'],
                    'users' => ['*'],
                ],
            ],
            'MemberController' => [
                [
                    'allow',
                    'users' => ['@'],
                ],
            ],
            'LoginApi' => [
                [
                    'deny',
                    'actions' => ['postIndex'],
                    'users' => ['@'],
                    'redirect' => '/'
                ],
                [
                    'allow',
                    'actions' => ['postIndex', 'postForgot', 'postRecover'],
                    'users' => ['?'],
                ],
                [
                    'allow',
                    'actions' => ['getIndex'],
                    'users' => ['@'],
                ],
                [
                    'deny',
                    'users' => ['*'],
                    'redirect' => '/',
                ],
            ],
            'UserApi' => [
                [
                    'allow',
                    'actions' => ['postIndex'],
                    'users' => ['?'], //allow anon to create account
                ],
                [
                    'allow',
                    'actions' => ['getIndex', 'putIndex'],
                    'users' => ['@'], //allow authed users to get index with their user info
                ],
                [
                    'allow',
                    'actions' => ['anyUniqueUsername', 'anyUniqueEmail'],
                    'users' => ['*'],
                ],
                [
                    'deny',
                    'users' => ['*'],
                    'redirect' => '/login',
                    'message' => "Access Not allowed"
                ],
            ],
            'ContactApi' => [
                [
                    'allow',
                    'actions' => ['postIndex'],
                    'users' => ['*'], //all users may create contact
                ],
                [
                    'deny',
                    'users' => ['*'],
                    'message' => "Access Not allowed"
                ],
            ],
            'SubscribeApi' => [
                [
                    'allow',
                    'actions' => ['postIndex', 'putIndex', 'deleteIndex'],
                    'users' => ['*'], //all users may create contact
                ],
                [
                    'deny',
                    'users' => ['*'],
                    'message' => "Access Not allowed"
                ],
            ],
            'ZipApi' => [
                [
                    'allow',
                    'actions' => ['getSelect2'],
                    'users' => ['*'], //all users may create contact
                ],
                [
                    'deny',
                    'users' => ['*'],
                    'message' => "Access Not allowed"
                ],
            ],
            'ExportApi' => [
				[
					'allow',
					'actions' => ['getIndex', 'postIndex'],
					'users' => ['@'], //allow authed users to get index with their user info
				],
				[
					'deny',
					'users' => ['*'],
					'message' => "Access Not allowed"
				],
			],
            'NotebookApi' => [
				[
					'allow',
					'users' => ['@'],
				],
				[
					'deny',
					'users' => ['*'],
					'message' => "Access Not allowed"
				],
			],
			'CountyApi' => [
				[
					'allow',
					'actions' => ['getDocuments'],
					'users' => ['@'], //allow authed users to get index with their user info
				],
				[
					'deny',
					'users' => ['*'],
					'message' => "Access Not allowed"
				],
			],
			'StateApi' => [
				[
					'allow',
					'actions' => ['getEndorsements'],
					'users' => ['@'], //allow authed users to get index with their user info
				],
				[
					'deny',
					'users' => ['*'],
					'message' => "Access Not allowed"
				],
			],
            'ContentApi' => [
            ],
            'DashboardController' => [
            ],
            'ElfinderController' => [
                [
                    'deny',
                    'action' => 'showIndex',
                    'users' => ['*'],
                ]
            ]
        ],
    ],
];