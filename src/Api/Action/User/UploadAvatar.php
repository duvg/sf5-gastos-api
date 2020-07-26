<?php

declare(strict_types=1);

namespace App\Api\Action\User;


use App\Entity\User;
use App\Service\File\ImageUploaderService;
use Symfony\Component\HttpFoundation\Request;

class UploadAvatar
{
    /**
     * @var ImageUploaderService
     */
    private ImageUploaderService $imageUploaderService;

    public function __construct(ImageUploaderService $imageUploaderService)
    {
        $this->imageUploaderService = $imageUploaderService;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, User $user): User
    {
        return $this->imageUploaderService->uploadAvatar($request, $user);
    }
}