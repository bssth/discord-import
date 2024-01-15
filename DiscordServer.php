<?php

use RestCord\DiscordClient;
use RestCord\Model\Channel\Channel;
use RestCord\Model\Channel\Message;
use RestCord\Model\Guild\Guild;

class DiscordServer
{
    /**
     * Bot token
     * @var string
     */
    protected $token;

    /**
     * @var DiscordClient
     */
    protected $api;

    /**
     * DiscordServer constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->api = new DiscordClient(['token' => $token]);
    }

    /**
     * @param int $server
     * @return Guild
     */
    public function getServerInfo(int $server): Guild
    {
        return $this->api->guild->getGuild(['guild.id' => $server]);
    }

    /**
     * @param int $server
     * @return Channel[]
     */
    public function getChannelsList(int $server): array
    {
        return $this->api->guild->getGuildChannels(['guild.id' => $server]);
    }

    /**
     * @param int $channel
     * @return Generator|Message[]
     */
    public function readChatHistory(int $channel)
    {
        $offset = 0;

        while(true) {
            $messages = $this->getChannelMessages($channel, $offset);

            if(is_array($messages) && count($messages)) {
                foreach($messages as $message) {
                    yield $message;
                    $offset = $message->id;
                }
            } else
                break;
        }
    }

    /**
     * @param int $channel
     * @param int $offset
     * @return Message[]
     */
    public function getChannelMessages(int $channel, int $offset = 0): array
    {
        $query = ['channel.id' => $channel, 'limit' => 100];
        if($offset !== 0)
            $query['before'] = $offset;

        return $this->api->channel->getChannelMessages($query);
    }
}