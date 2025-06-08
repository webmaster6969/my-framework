<?php

namespace UnitMock;

use App\domain\Auth\Domain\Model\Entities\User;
use Core\Database\DB;
use Core\Support\Auth\Auth;
use Core\Support\Session\Session;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testAuthSuccess(): void
    {
        $email = 'test@example.com';
        $password = 'secret';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getPassword')->andReturn($hashedPassword);
        $user->shouldReceive('getId')->andReturn(1);

        $em = Mockery::mock(EntityManagerInterface::class);

        $metadata = new ClassMetadata(User::class);

        $repository = new EntityRepository($em, $metadata);
        $repository = Mockery::mock($repository)->makePartial();
        $repository->shouldReceive('findOneBy')->with(['email' => $email])->andReturn($user);

        $em->shouldReceive('getRepository')->with(User::class)->andReturn($repository);

        Mockery::mock('alias:' . DB::class)
            ->shouldReceive('getEntityManager')
            ->andReturn($em);

        Mockery::mock('alias:' . Session::class)
            ->shouldReceive('set')
            ->with('user_id', 1)
            ->once();

        $this->assertTrue(Auth::auth($email, $password));
    }

    /**
     * @return void
     */
    public function testAuthFailsWithWrongPassword(): void
    {
        $email = 'test@example.com';
        $password = 'wrong-password';
        $hashedPassword = password_hash('correct-password', PASSWORD_BCRYPT);

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getPassword')->andReturn($hashedPassword);

        $em = Mockery::mock(EntityManagerInterface::class);

        $metadata = new ClassMetadata(User::class);

        $repository = new EntityRepository($em, $metadata);
        $repository = Mockery::mock($repository)->makePartial();
        $repository->shouldReceive('findOneBy')->with(['email' => $email])->andReturn($user);

        $em->shouldReceive('getRepository')->with(User::class)->andReturn($repository);

        Mockery::mock('alias:' . DB::class)
            ->shouldReceive('getEntityManager')
            ->andReturn($em);

        Mockery::mock('alias:' . Session::class)
            ->shouldReceive('set')
            ->never();

        $this->assertFalse(Auth::auth($email, $password));
    }
}