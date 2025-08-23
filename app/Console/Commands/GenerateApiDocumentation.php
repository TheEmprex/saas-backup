<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class GenerateApiDocumentation extends Command
{
    protected $signature = 'api:docs {--output=docs/api.json : Output file path}';
    protected $description = 'Generate OpenAPI 3.0 documentation for the API';

    private $openApiDoc = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'SaaS Platform API',
            'description' => 'Comprehensive API documentation for our SaaS platform',
            'version' => '1.0.0',
            'contact' => [
                'name' => 'API Support',
                'email' => 'api-support@example.com'
            ]
        ],
        'servers' => [
            [
                'url' => 'https://api.example.com',
                'description' => 'Production server'
            ],
            [
                'url' => 'https://staging.api.example.com',
                'description' => 'Staging server'
            ],
            [
                'url' => 'http://localhost:8000',
                'description' => 'Development server'
            ]
        ],
        'paths' => [],
        'components' => [
            'schemas' => [],
            'responses' => [],
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT'
                ],
                'sanctumAuth' => [
                    'type' => 'apiKey',
                    'in' => 'header',
                    'name' => 'Authorization'
                ]
            ]
        ],
        'security' => [
            ['bearerAuth' => []],
            ['sanctumAuth' => []]
        ]
    ];

    public function handle()
    {
        $this->info('Generating API documentation...');

        $this->addCommonSchemas();
        $this->addCommonResponses();
        
        $apiRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return Str::startsWith($route->uri(), 'api/');
        });

        $this->info("Found {$apiRoutes->count()} API routes");

        foreach ($apiRoutes as $route) {
            $this->processRoute($route);
        }

        $outputPath = $this->option('output');
        $this->ensureDirectoryExists(dirname($outputPath));
        
        file_put_contents($outputPath, json_encode($this->openApiDoc, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        
        $this->info("API documentation generated: {$outputPath}");

        // Also generate HTML documentation
        $this->generateHtmlDocs($outputPath);
    }

    private function processRoute($route)
    {
        $uri = $route->uri();
        $methods = $route->methods();
        $action = $route->getAction();

        // Skip non-documented routes
        if (in_array('HEAD', $methods) || in_array('OPTIONS', $methods)) {
            return;
        }

        $path = '/' . ltrim($uri, '/');
        $path = preg_replace('/\{([^}]+)\}/', '{\1}', $path); // Ensure parameter format

        if (!isset($this->openApiDoc['paths'][$path])) {
            $this->openApiDoc['paths'][$path] = [];
        }

        foreach ($methods as $method) {
            if (in_array($method, ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
                $this->openApiDoc['paths'][$path][strtolower($method)] = $this->buildOperation($route, $method, $action);
            }
        }
    }

    private function buildOperation($route, $method, $action)
    {
        $controllerClass = $action['controller'] ?? null;
        $operationId = $this->generateOperationId($route->uri(), $method);
        
        $operation = [
            'operationId' => $operationId,
            'summary' => $this->generateSummary($route->uri(), $method),
            'description' => $this->generateDescription($route->uri(), $method),
            'tags' => [$this->generateTag($route->uri())],
            'responses' => $this->getDefaultResponses($method),
        ];

        // Add authentication requirement for protected routes
        if ($this->requiresAuth($route)) {
            $operation['security'] = [['bearerAuth' => []], ['sanctumAuth' => []]];
        }

        // Add parameters
        $parameters = $this->extractParameters($route->uri());
        if (!empty($parameters)) {
            $operation['parameters'] = $parameters;
        }

        // Add request body for POST/PUT/PATCH
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $operation['requestBody'] = $this->generateRequestBody($route->uri(), $method);
        }

        // Try to get more detailed info from controller
        if ($controllerClass && method_exists($controllerClass, $action['method'] ?? '')) {
            $reflection = $this->getControllerReflection($controllerClass, $action['method']);
            if ($reflection) {
                $operation = array_merge($operation, $reflection);
            }
        }

        return $operation;
    }

    private function generateOperationId($uri, $method)
    {
        $parts = explode('/', trim($uri, '/'));
        $parts = array_filter($parts, function($part) {
            return !preg_match('/\{.*\}/', $part) && $part !== 'api';
        });
        
        return strtolower($method) . Str::studly(implode('_', $parts));
    }

    private function generateSummary($uri, $method)
    {
        $resource = $this->getResourceFromUri($uri);
        
        $summaries = [
            'GET' => "Retrieve {$resource}",
            'POST' => "Create {$resource}",
            'PUT' => "Update {$resource}",
            'PATCH' => "Partially update {$resource}",
            'DELETE' => "Delete {$resource}",
        ];

        return $summaries[$method] ?? "Perform {$method} operation";
    }

    private function generateDescription($uri, $method)
    {
        $resource = $this->getResourceFromUri($uri);
        
        $descriptions = [
            'GET' => "Retrieve information about {$resource}. Supports filtering and pagination.",
            'POST' => "Create a new {$resource}. All required fields must be provided.",
            'PUT' => "Update an existing {$resource}. All fields will be updated.",
            'PATCH' => "Partially update an existing {$resource}. Only provided fields will be updated.",
            'DELETE' => "Delete an existing {$resource}. This action cannot be undone.",
        ];

        return $descriptions[$method] ?? "Perform {$method} operation on {$resource}";
    }

    private function generateTag($uri)
    {
        $parts = explode('/', trim($uri, '/'));
        
        // Remove 'api' and version prefix
        $parts = array_filter($parts, function($part) {
            return $part !== 'api' && !preg_match('/^v\d+$/', $part);
        });

        $resource = reset($parts);
        return Str::title(str_replace('-', ' ', $resource));
    }

    private function getResourceFromUri($uri)
    {
        $parts = explode('/', trim($uri, '/'));
        $resource = '';
        
        foreach ($parts as $part) {
            if (!preg_match('/\{.*\}/', $part) && $part !== 'api' && !preg_match('/^v\d+$/', $part)) {
                $resource = $part;
                break;
            }
        }
        
        return Str::singular($resource) ?: 'resource';
    }

    private function extractParameters($uri)
    {
        $parameters = [];
        
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);
        
        foreach ($matches[1] as $param) {
            $parameters[] = [
                'name' => $param,
                'in' => 'path',
                'required' => true,
                'schema' => [
                    'type' => $this->getParameterType($param),
                    'example' => $this->getParameterExample($param)
                ],
                'description' => $this->getParameterDescription($param)
            ];
        }

        return $parameters;
    }

    private function getParameterType($param)
    {
        if (Str::endsWith($param, '_id') || $param === 'id') {
            return 'integer';
        }
        
        return 'string';
    }

    private function getParameterExample($param)
    {
        if (Str::endsWith($param, '_id') || $param === 'id') {
            return 1;
        }
        
        return 'example-' . $param;
    }

    private function getParameterDescription($param)
    {
        $descriptions = [
            'id' => 'Unique identifier',
            'user_id' => 'User identifier',
            'conversation_id' => 'Conversation identifier',
            'message_id' => 'Message identifier',
            'job_id' => 'Job post identifier',
        ];

        return $descriptions[$param] ?? "The {$param} parameter";
    }

    private function generateRequestBody($uri, $method)
    {
        $resource = $this->getResourceFromUri($uri);
        
        return [
            'required' => true,
            'content' => [
                'application/json' => [
                    'schema' => [
                        '$ref' => "#/components/schemas/{$resource}Request"
                    ]
                ],
                'multipart/form-data' => [
                    'schema' => [
                        '$ref' => "#/components/schemas/{$resource}FormRequest"
                    ]
                ]
            ]
        ];
    }

    private function getDefaultResponses($method)
    {
        $responses = [
            '400' => ['$ref' => '#/components/responses/BadRequest'],
            '401' => ['$ref' => '#/components/responses/Unauthorized'],
            '403' => ['$ref' => '#/components/responses/Forbidden'],
            '500' => ['$ref' => '#/components/responses/InternalServerError'],
        ];

        switch ($method) {
            case 'GET':
                $responses['200'] = ['$ref' => '#/components/responses/Success'];
                $responses['404'] = ['$ref' => '#/components/responses/NotFound'];
                break;
            case 'POST':
                $responses['201'] = ['$ref' => '#/components/responses/Created'];
                $responses['422'] = ['$ref' => '#/components/responses/ValidationError'];
                break;
            case 'PUT':
            case 'PATCH':
                $responses['200'] = ['$ref' => '#/components/responses/Success'];
                $responses['404'] = ['$ref' => '#/components/responses/NotFound'];
                $responses['422'] = ['$ref' => '#/components/responses/ValidationError'];
                break;
            case 'DELETE':
                $responses['200'] = ['$ref' => '#/components/responses/Success'];
                $responses['404'] = ['$ref' => '#/components/responses/NotFound'];
                break;
        }

        return $responses;
    }

    private function requiresAuth($route)
    {
        $middleware = $route->middleware();
        return in_array('auth:sanctum', $middleware) || 
               in_array('auth', $middleware) || 
               in_array('auth:api', $middleware);
    }

    private function getControllerReflection($controllerClass, $method)
    {
        try {
            $reflection = new ReflectionClass($controllerClass);
            $methodReflection = $reflection->getMethod($method);
            $docComment = $methodReflection->getDocComment();
            
            if ($docComment) {
                return $this->parseDocComment($docComment);
            }
        } catch (\Exception $e) {
            // Ignore reflection errors
        }

        return [];
    }

    private function parseDocComment($docComment)
    {
        $info = [];
        
        if (preg_match('/@summary\s+(.+)/', $docComment, $matches)) {
            $info['summary'] = trim($matches[1]);
        }
        
        if (preg_match('/@description\s+(.+)/', $docComment, $matches)) {
            $info['description'] = trim($matches[1]);
        }
        
        return $info;
    }

    private function addCommonSchemas()
    {
        $this->openApiDoc['components']['schemas'] = [
            'User' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'name' => ['type' => 'string', 'example' => 'John Doe'],
                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'john@example.com'],
                    'avatar' => ['type' => 'string', 'nullable' => true, 'example' => 'https://example.com/avatar.jpg'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'Conversation' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'type' => ['type' => 'string', 'enum' => ['direct', 'group'], 'example' => 'direct'],
                    'name' => ['type' => 'string', 'nullable' => true, 'example' => 'Project Discussion'],
                    'participants' => [
                        'type' => 'array',
                        'items' => ['$ref' => '#/components/schemas/User']
                    ],
                    'last_message' => ['$ref' => '#/components/schemas/Message'],
                    'unread_count' => ['type' => 'integer', 'example' => 3],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'Message' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'conversation_id' => ['type' => 'integer', 'example' => 1],
                    'sender_id' => ['type' => 'integer', 'example' => 1],
                    'content' => ['type' => 'string', 'example' => 'Hello, how are you?'],
                    'type' => ['type' => 'string', 'enum' => ['text', 'image', 'file', 'voice'], 'example' => 'text'],
                    'sender' => ['$ref' => '#/components/schemas/User'],
                    'reply_to_id' => ['type' => 'integer', 'nullable' => true, 'example' => null],
                    'reactions' => [
                        'type' => 'array',
                        'items' => ['$ref' => '#/components/schemas/MessageReaction']
                    ],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'MessageReaction' => [
                'type' => 'object',
                'properties' => [
                    'emoji' => ['type' => 'string', 'example' => '👍'],
                    'user_id' => ['type' => 'integer', 'example' => 1],
                    'user' => ['$ref' => '#/components/schemas/User']
                ]
            ],
            'JobPost' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer', 'example' => 1],
                    'title' => ['type' => 'string', 'example' => 'Senior Developer Position'],
                    'description' => ['type' => 'string', 'example' => 'We are looking for an experienced developer...'],
                    'hourly_rate' => ['type' => 'number', 'format' => 'decimal', 'example' => 50.00],
                    'status' => ['type' => 'string', 'enum' => ['active', 'inactive', 'closed'], 'example' => 'active'],
                    'user' => ['$ref' => '#/components/schemas/User'],
                    'created_at' => ['type' => 'string', 'format' => 'date-time'],
                    'updated_at' => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'Error' => [
                'type' => 'object',
                'properties' => [
                    'message' => ['type' => 'string', 'example' => 'An error occurred'],
                    'error' => ['type' => 'string', 'example' => 'validation_error'],
                    'status_code' => ['type' => 'integer', 'example' => 422],
                    'errors' => [
                        'type' => 'object',
                        'additionalProperties' => [
                            'type' => 'array',
                            'items' => ['type' => 'string']
                        ]
                    ]
                ]
            ],
            'PaginationMeta' => [
                'type' => 'object',
                'properties' => [
                    'current_page' => ['type' => 'integer', 'example' => 1],
                    'from' => ['type' => 'integer', 'example' => 1],
                    'last_page' => ['type' => 'integer', 'example' => 10],
                    'per_page' => ['type' => 'integer', 'example' => 15],
                    'to' => ['type' => 'integer', 'example' => 15],
                    'total' => ['type' => 'integer', 'example' => 150]
                ]
            ]
        ];
    }

    private function addCommonResponses()
    {
        $this->openApiDoc['components']['responses'] = [
            'Success' => [
                'description' => 'Successful operation',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => ['type' => 'string', 'example' => 'Operation successful'],
                                'data' => ['type' => 'object']
                            ]
                        ]
                    ]
                ]
            ],
            'Created' => [
                'description' => 'Resource created successfully',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'message' => ['type' => 'string', 'example' => 'Resource created successfully'],
                                'data' => ['type' => 'object']
                            ]
                        ]
                    ]
                ]
            ],
            'BadRequest' => [
                'description' => 'Bad request',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ],
            'Unauthorized' => [
                'description' => 'Authentication required',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ],
            'Forbidden' => [
                'description' => 'Access forbidden',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ],
            'NotFound' => [
                'description' => 'Resource not found',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ],
            'ValidationError' => [
                'description' => 'Validation error',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ],
            'RateLimited' => [
                'description' => 'Rate limit exceeded',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ],
            'InternalServerError' => [
                'description' => 'Internal server error',
                'content' => [
                    'application/json' => [
                        'schema' => ['$ref' => '#/components/schemas/Error']
                    ]
                ]
            ]
        ];
    }

    private function ensureDirectoryExists($directory)
    {
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    private function generateHtmlDocs($jsonPath)
    {
        $htmlPath = str_replace('.json', '.html', $jsonPath);
        
        $html = $this->getSwaggerUITemplate($jsonPath);
        file_put_contents($htmlPath, $html);
        
        $this->info("HTML documentation generated: {$htmlPath}");
    }

    private function getSwaggerUITemplate($jsonPath)
    {
        $jsonUrl = basename($jsonPath);
        
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }
        *, *:before, *:after {
            box-sizing: inherit;
        }
        body {
            margin:0;
            background: #fafafa;
        }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: '{$jsonUrl}',
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout"
            });
        };
    </script>
</body>
</html>
HTML;
    }
}
