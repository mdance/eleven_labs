<?php

namespace Drupal\eleven_labs;

/**
 * Provides the ElevenLabsConstants class.
 */
class ElevenLabsConstants {

  /**
   * Provides the responses table.
   *
   * @var string
   */
  public const TABLE_RESPONSES = 'eleven_labs_responses';

  /**
   * Provides the state key.
   */
  public const KEY_STATE = 'eleven_labs';

  /**
   * Provides the configuration key.
   *
   * @var string
   */
  public const KEY_SETTINGS = 'eleven_labs.settings';

  /**
   * Provides the schema key.
   */
  public const KEY_SCHEMA = 'schema';

  /**
   * Provides the host key.
   */
  public const KEY_HOST = 'host';

  /**
   * Provides the api key key.
   *
   * @var string
   */
  public const KEY_API_KEY = 'api_key';

  /**
   * Provides the api key header.
   *
   * @var string
   */
  public const KEY_API_KEY_HEADER = 'xi-api-key';

  /**
   * Provides the https schema.
   *
   * @var string
   */
  public const SCHEMA = 'https';

  /**
   * Provides the host.
   *
   * @var string
   */
  public const HOST = 'api.elevenlabs.io';

  /**
   * Provides the voices path.
   *
   * @var string
   */
  public const PATH_VOICES = '/v1/voices';

  /**
   * Provides the voice path.
   *
   * @var string
   */
  public const PATH_VOICE = '/v1/voices/{voice_id}';

  /**
   * Provides the add voice path.
   *
   * @var string
   */
  public const PATH_VOICE_ADD = '/v1/voices/add';

  /**
   * Provides the text to speech path.
   *
   * @var string
   */
  public const PATH_TEXT_TO_SPEECH = '/v1/text-to-speech/{voice_id}';

  /**
   * Provides the get history path.
   *
   * @var string
   */
  public const PATH_HISTORY = '/v1/history';

  /**
   * Provides the audio history path.
   *
   * @var string
   */
  public const PATH_AUDIO_HISTORY = '/v1/history/{history_item_id}/audio';

  /**
   * Provides the history item path.
   *
   * @var string
   */
  public const PATH_HISTORY_ITEM = '/v1/history/{history_item_id}';

  /**
   * Provides the download history path.
   */
  public const PATH_HISTORY_DOWNLOAD = '/v1/history/download';

  /**
   * Provides the user info path.
   */
  public const PATH_USER_INFO = '/v1/user';

  /**
   * Provides the view voices permission.
   *
   * @var string
   */
  public const PERMISSION_VIEW_VOICES = 'view eleven_labs voices';

  /**
   * Provides the edit voices permission.
   *
   * @var string
   */
  public const PERMISSION_EDIT_VOICES = 'edit eleven_labs voices';

  /**
   * Provides the delete voices permission.
   *
   * @var string
   */
  public const PERMISSION_DELETE_VOICES = 'delete eleven_labs voices';

  /**
   * Provides the view history permission.
   *
   * @var string
   */
  public const PERMISSION_VIEW_HISTORY = 'view eleven_labs history';

  /**
   * Provides the download history permission.
   *
   * @var string
   */
  public const PERMISSION_DOWNLOAD_HISTORY = 'download eleven_labs history';

  /**
   * Provides the delete history permission.
   *
   * @var string
   */
  public const PERMISSION_DELETE_HISTORY = 'delete eleven_labs history';

  /**
   * Provides the view user information permission.
   *
   * @var string
   */
  public const PERMISSION_USER = 'view eleven_labs user';

  /**
   * Provides the default stability.
   */
  public const DEFAULT_STABILITY = 0;

  /**
   * Provides the default similarity boost.
   */
  public const DEFAULT_SIMILARITY_BOOST = 0;

}
