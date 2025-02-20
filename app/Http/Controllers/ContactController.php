<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Orchid\Attachment\File;

class ContactController
{
    public function all(Request $request)
    {
        try {
            if ($request->consultantId) {
                $contacts = Contact::where('contacts.user_id', $request->consultantId)->orderBy('contacts.name', 'asc')
                    ->select('contacts.id', 'contacts.name')->get();
            }
            if (authUserInRole('bireysel-danisman')) {
                $contacts = Contact::where('user_id', auth()->user()->id)
                    ->orderBy('contacts.name', 'asc')
                    ->select('contacts.id', 'contacts.name')->get();
            }

            if ($contacts->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            return response([
                'status' => true,
                'message' => "Kişi verileri aktarımı başarılı.",
                'data' => $contacts
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing contacts.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $page = $request->page;
            $itemsPerPage = $request->itemsPerPage;

            if (authUserInRole(['super-yonetici', 'yonetici'])) {
                $contacts = Contact::orderBy('name', 'asc')->paginate($itemsPerPage, ['*'], 'page', $page);
            }
            if (authUserInRole('ofis-yoneticisi')) {
                $contacts = Contact::join('users', 'users.id', '=', 'contacts.user_id')
                    ->where('users.office_id', auth()->user()->office_id)
                    ->orderBy('contacts.name', 'asc')
                    ->select('contacts.*')
                    ->paginate($itemsPerPage, ['*'], 'page', $page);
            }
            if (authUserInRole(['ofis-danismani', 'bireysel-danisman'])) {
                $contacts = Contact::where('user_id', auth()->user()->id)
                    ->orderBy('contacts.name', 'asc')
                    ->select('contacts.*')
                    ->paginate($itemsPerPage, ['*'], 'page', $page);
            }

            if ($contacts->count() == 0) {
                return response([
                    'status' => false,
                    'message' => "Herhangi bir kayıt bulunamadı."
                ], 404);
            }

            foreach ($contacts as $key => $contact) {
                $contactAttachments = $contact->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $contactAttachments);

                $contact->attachments = $result;
            }

            return response([
                'status' => true,
                'config' => ['page' => $page, 'itemsPerPage' => $itemsPerPage],
                'message' => "Kişi verileri aktarımı başarılı.",
                'data' => $contacts
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during listing contact.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            $contact = Contact::find($request->contactId);

            if (!$contact) {
                return response([
                    'status' => false,
                    'message' => "Kişi bulunamadı"
                ], 404);
            }
            $contactAttachment = $contact->attachment()->get()->toArray();
            $result = array_map(function ($attachment) {
                return [
                    'mime' => $attachment['mime'],
                    'extension' => $attachment['extension'],
                    'url' => $attachment['url'],
                    'name' => $attachment['name'],
                    'id' => $attachment['id'],
                ];
            }, $contactAttachment);

            $contact->attachments = $result;

            return response([
                'status' => true,
                'message' => "Kişi bilgisi aktarımı başarılı",
                'data' => $contact
            ], 200);
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during get contact detail.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            if ($request->file('avatar')) {

                $file = new File($request->file('avatar'));
                $attachment = $file->allowDuplicates()->load();

                $contactRequest = $request->all();
                $contactRequest['avatar'] = $attachment;
                $request->replace($contactRequest);

                $newContact = new Contact();
                if ($request->has('consultant_id')) {
                    $newContact->user_id = $request->consultant_id;
                } else {
                    $newContact->user_id = auth()->user()->id;
                }
                $newContact
                    ->fill($request->toArray())
                    ->fill(['avatar' => $attachment->id])
                    ->save();

                $newContact->attachment()->syncWithoutDetaching(
                    $request->input('avatar', [])
                );

                $contactAttachment = $newContact->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $contactAttachment);

                $newContact->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "Contact created with avatar.",
                    'data' => $newContact
                ], 200);
            } else {
                $newContact = new Contact();
                if ($request->has('consultant_id')) {
                    $newContact->user_id = $request->consultant_id;
                } else {
                    $newContact->user_id = auth()->user()->id;
                }
                $newContact
                    ->fill($request->all())
                    ->save();

                $newContact->load('attachment');

                return response([
                    'status' => true,
                    'message' => "Contact created without avatar.",
                    'data' => $newContact
                ], 200);
            }
        } catch (\Exception $error) {

            return response([
                'status' => false,
                'message' => "Something went wrong during creating contact.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $contact = Contact::find((int) $request->contactId);

            if (!$contact) {
                return response([
                    'status' => false,
                    'message' => "Kişi bulunamadı"
                ], 404);
            }
            if ($request->file('avatar')) {
                if ($contact->attachment()->first()) {
                    $contact->attachment[0]->delete();
                }

                $file = new File($request->file('avatar'));
                $attachment = $file->allowDuplicates()->load();

                $contactRequest = $request->all();
                $contactRequest['avatar'] = $attachment;
                $request->replace($contactRequest);
                if ($request->has('consultant_id')) {
                    $contact->user_id = $request->consultant_id;
                } else {
                    $contact->user_id = auth()->user()->id;
                }
                $contact
                    ->fill($request->all())
                    ->fill(['avatar' => $attachment->id])
                    ->save();

                $contact->attachment()->syncWithoutDetaching(
                    $request->input('avatar', [])
                );

                $contactAttachment = $contact->attachment()->get()->toArray();
                $result = array_map(function ($attachment) {
                    return [
                        'mime' => $attachment['mime'],
                        'extension' => $attachment['extension'],
                        'url' => $attachment['url'],
                        'name' => $attachment['name'],
                        'id' => $attachment['id'],
                    ];
                }, $contactAttachment);

                $contact->attachments = $result;

                return response([
                    'status' => true,
                    'message' => "Contact updated with avatar.",
                    'data' => $contact
                ], 200);
            } else {
                if ($request->has('consultant_id')) {
                    $contact->user_id = $request->consultant_id;
                } else {
                    $contact->user_id = auth()->user()->id;
                }
                $contact
                    ->fill($request->all())
                    ->save();

                $contact->load('attachment');

                return response([
                    'status' => true,
                    'message' => "Contact updated without avatar.",
                    'data' => $contact
                ], 200);
            }
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => "Something went wrong during saving contact.",
                'error' => $error->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $contact = Contact::find($request->contactId);

            if (!$contact) {
                return response([
                    'status' => false,
                    'message' => "Kişi bulunamadı"
                ], 404);
            }
            $contact->attachment->each->delete();
            $contact->delete();

            return response([
                'status' => true,
                'message' => 'Kişi başarıyla silindi'
            ], 200);
        } catch (\Exception $error) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
