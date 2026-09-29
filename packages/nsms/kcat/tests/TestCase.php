<?php
namespace KcatTransferTests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as LaravelTestCase;
use Illuminate\Contracts\Console\Kernel;
use Nsms\Kcat\KcatServiceProvider;

abstract class TestCase extends LaravelTestCase
{
    public function createApplication(): Application
    {
        $base = __DIR__.'/work';
        foreach (['bootstrap/cache', 'storage/framework/views', 'storage/framework/sessions', 'storage/framework/cache', 'storage/logs', 'config'] as $dir) {
            if (! is_dir($base.'/'.$dir)) { mkdir($base.'/'.$dir, 0777, true); }
        }
        if (! is_file($base.'/bootstrap/providers.php')) { file_put_contents($base.'/bootstrap/providers.php', '<?php return [];'); }
        file_put_contents($base.'/composer.json', json_encode(['autoload' => ['psr-4' => ['App\\' => 'app/']]]));
        $app = Application::configure(basePath: $base)->withRouting(using: static function (): void {})
            ->withMiddleware()->withExceptions()
            ->withProviders([KcatServiceProvider::class])->create();
        $app->make(Kernel::class)->bootstrap();
        $app['config']->set([
            'database.default' => 'kcat_test',
            'database.connections.kcat_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array', 'auth.providers.users.model' => TestUser::class,
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)), 'app.debug' => true,
        ]);
        return $app;
    }
}
